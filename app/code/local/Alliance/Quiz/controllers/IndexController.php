<?php
class Alliance_Quiz_IndexController extends Mage_Core_Controller_Front_Action
{
    public function indexAction()
    {
    	
    	/*
    	 * Load an object by id 
    	 * Request looking like:
    	 * http://site.com/quiz?id=15 
    	 *  or
    	 * http://site.com/quiz/id/15 	
    	 */
    	/* 
		$quiz_id = $this->getRequest()->getParam('id');

  		if($quiz_id != null && $quiz_id != '')	{
			$quiz = Mage::getModel('quiz/quiz')->load($quiz_id)->getData();
		} else {
			$quiz = null;
		}	
		*/
		
		 /*
    	 * If no param we load a the last created item
    	 */ 
    	/*
    	if($quiz == null) {
			$resource = Mage::getSingleton('core/resource');
			$read= $resource->getConnection('core_read');
			$quizTable = $resource->getTableName('quiz');
			
			$select = $read->select()
			   ->from($quizTable,array('quiz_id','title','content','status'))
			   ->where('status',1)
			   ->order('created_time DESC') ;
			   
			$quiz = $read->fetchRow($select);
		}
		Mage::register('quiz', $quiz);
		*/

			
		$this->loadLayout();     
		$this->renderLayout();
    }
	public function saveAction(){
			


		if ($data = $this->getRequest()->getPost()) {		
            $model = Mage::getModel('quiz/quiz');
//			print_r($data);
			//echo $data[2];
			foreach($data as $key=>$value )
			{
				if(is_array($data[$key]))
					$data[$key]	=	$this->returnCheckboxArray($data[$key]);
			}

            		$model->setData($data)
           			         ->setId($this->getRequest()->getParam('id'));
            		try {
							if ($model->getCreatedTime == NULL || $model->getUpdateTime() == NULL) {
								$model->setCreatedTime(now())
										->setUpdateTime(now());
							} else {
								$model->setUpdateTime(now());
							}
			
					$model->save();





					$option1	=	str_replace(',','-',$data['q1option']);
					$option1	=	str_replace('Fine Lines and Wrinkles','fine-lines',$option1);
					$option1	=	str_replace('Acne and Blemishes','acne',$option1);
					$option1	=	str_replace('Dark Spots and Discoloration','discoloration',$option1);
					$option1	=	str_replace('Sensitivity','sensitive',$option1);
					$option1	=	str_replace('Enlarged Pores','enlarged-pores',$option1);
					
					$option2	=	str_replace(',','-',$data['q2option']);
					$option2	=	str_replace('Normal','normal',$option2);
					$option2	=	str_replace('Dry or Very Dry','dry',$option2);
					$option2	=	str_replace('Oily or Very Oily','oily',$option2);
					$option2	=	str_replace('Oily in Certain Areas','combo',$option2);

					$option4	=	str_replace(',','-',$data['q4option']);
					$option4	=	str_replace('Never Sensitive','never-rarely-some',$option4);
					$option4	=	str_replace('Rarely Sensitive','never-rarely-some',$option4);
					$option4	=	str_replace('Sensitive Sometimes','never-rarely-some',$option4);
					$option4	=	str_replace('Sensitive Most of the Time','most-all',$option4);
					$option4	=	str_replace('Sensitive All of the Time','most-all',$option4);
					

					$option5	=	str_replace(',','-',$data['q5option']);
					$option5	=	str_replace('Wrinkles and/or Dryness','line',$option5);
					$option5	=	str_replace('Dark Circles and/or Puffiness','cyto',$option5);


					if($option1	==	"fine-lines-sensitive" || $option1	==	"acne" || $option1	==	"sensitive"){
					
					//}elseif($option1	==	"fine-lines-wrinkles-sensitive"){
					}else{
						if($option2=="normal" || $option2=="dry")
							$option2	=	"dry-normal";
						elseif($option2=="oily" || $option2=="combo")	
							$option2	=	"oily-combo";
					}
					
					
					/*if($option4=="never" || $option4=="rarely")
						$option4	=	"never-rarely";
					elseif($option4=="some" || $option4=="most" || $option4=="all")
						$option4	=	"some-most-all";
						*/
					
					
					$option_url	=	$option1."-".$option2."-".$option4."-".$option5;
					
/*
					if($option1	==	"acne")
						$option_url	=	$option1."-".$option4."-".$option5;
					elseif($option1	==	"sensitive")
					{
						 if($option2=="normal" || $option2=="dry" ||  $option2=="combo")
							$option_url	=	$option1."-".$option4."-".$option5;
					}

*/
						

					if($option1	==	"acne")
						$option_url	=	$option1."-".$option4."-".$option5;
					elseif($option1	==	"sensitive")
					{
						 if($option2=="normal" || $option2=="dry" ||  $option2=="combo")
							$option_url	=	$option1."-".$option4."-".$option5;
					}

					elseif($option1	==	"fine-lines-sensitive")
					{
						 if($option2=="oily" ||  $option2=="combo")
							$option_url	=	$option1."-oily-combo-".$option4."-".$option5;
					}

			
					echo Mage::getUrl("skin-evaluation/".$option_url);
					Mage::getModel("core/cookie")->set('applicant', $data['qname'], '3265920000');
					Mage::getModel("core/cookie")->set('currenturl', Mage::getUrl("skin-evaluation/".$option_url), '3265920000');


				} catch (Exception $e) {
					return;
				}
			//echo "end";
		}	
	}
	public function returnCheckboxArray($optionarray)
	{
		$option	=	"";
		for ($i=0; $i<count($optionarray); $i++) {
			if($i==0)
				$option	=	$optionarray[$i];
			else
				$option	=	$option.",".$optionarray[$i];
		}
		return $option;
	}

}