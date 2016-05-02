<?php 
require_once 'lib/swift_required.php';   
class Alliance_Kiosk_ApiController extends Mage_Core_Controller_Front_Action {


    

    public function indexAction()
    {
      
        
    }
    // Starts session. Clears out all the data saved before
    public function startAction() {
      $session = Mage::getSingleton('core/session');
      
        $session->setData('savedBefore', "");
        $session->setData('lastId', "");
        $session->setData('results', "");

    }
    
    public function setDataAction() {
      $postData = json_decode(file_get_contents("php://input"));
      Mage::getSingleton('core/session')->setData('savedBefore', $postData);
    }
    
    
    public function finishAction() {
      $savedBefore = Mage::getSingleton('core/session')->getData('savedBefore');
      $postData = json_decode(file_get_contents("php://input"));
      foreach($savedBefore as $key=>$value) {
        if (!is_numeric($key)) {
          $results[$key] = $value;
        } else {
          $results['answers'][] = $key;
        }
      }
      foreach($postData as $key=>$value) {$results['answers'][] = $key;}

      $z_store_ipad_name = isset($_COOKIE["store_ipad_id"]) ? explode( "-", $_COOKIE["store_ipad_id"] ) : array("undefined") ;

      Mage::getSingleton('core/session')->setData('results', $results);
      $write = Mage::getSingleton('core/resource')->getConnection('core_write'); 
      $query = "INSERT INTO `nordstrom_quiz_completed` (location, username, age, gender, customer_id, date_completed)
            VALUES ('".$z_store_ipad_name[0]."','". $results['name']."','". $results['age']."', '". $results['gender']."', 0, '". date("Y-m-d H:i:s") ."')";
      $write->query($query);
      $lastId = $write->fetchOne('SELECT last_insert_id()');
      Mage::getSingleton('core/session')->setData('lastId', $lastId);
      foreach ( $results['answers'] as $ansId ) {
        $query = "SELECT question_id FROM `nordstrom_quiz_answers` WHERE id = $ansId"; 
        $qId = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        $query = "SELECT answer_text FROM `nordstrom_quiz_answers` WHERE id = $ansId"; 
        $aText = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        $query = "SELECT text FROM `nordstrom_quiz_questions` WHERE id = $qId"; 
        $qText = Mage::getSingleton('core/resource')->getConnection('core_read')->fetchOne($query);
        
        $query = "INSERT INTO `nordstrom_quiz_completed_details` (answers_group, question_id, question_text, answer_text) 
            VALUES (". $lastId .", " . $qId . ", '". $qText."', '". $aText."')";
        $write->query($query);
      }
      
    }
    
    // Return first 5 questions
    public function getAboutYouQuestionsAction() {
      $sql = Mage::getSingleton('core/resource')->getConnection('core_read');
      $questions = $sql->fetchAll("SELECT * FROM `nordstrom_quiz_questions` WHERE 1 ORDER BY sort_order ASC LIMIT 0, 4");
      foreach ($questions as $key => $q) {
      // Generate questions
        $result[$q['id']]['text'] = $q['text'];
        $result[$q['id']]['id'] = $q['id'];
        $result[$q['id']]['single'] = $q['single_answer'];
        if (strlen($q['hint_title']) > 1) {
        $result[$q['id']]['hint_title'] = $q['hint_title'];
        $result[$q['id']]['hint_text'] = $q['hint_text'];
        }
        $id = $q['id'];
        $answers = $sql->fetchAll("SELECT * FROM `nordstrom_quiz_answers` WHERE question_id = $id");
        foreach($answers as $key => $a) {
          // Add some answers
          // Bugfix: Has to provide id in array instead of a key
          $result[$q['id']]['answers'][$key]['id'] = $a['id'];
          $result[$q['id']]['answers'][$key]['text'] = $a['answer_text'];
        }
      }
    echo json_encode($result); // And send it back!
    }
    
    // Return last 5 questons
    public function getSkinConcernQuestionsAction() {
    /* Ans id. - Ques. id 
    102 - Acne    33
    103 - Discol. 35
    104 - Lines   34
    */
    $sql = Mage::getSingleton('core/resource')->getConnection('core_read');
    $savedBefore = Mage::getSingleton('core/session')->getData('savedBefore');

$savedBefore = (array)$savedBefore;

$savedVar = array();
foreach( $savedBefore as $key => $x ){
$savedVar[$key] = $x;
}

$questions = $sql->fetchAll("SELECT * FROM `nordstrom_quiz_questions` WHERE 1 ORDER BY sort_order ASC LIMIT 4, 10");
    
    foreach ($questions as $key => $q) {
      // IF CLAUSE 0830131717CL_Conditional evaluation questions FIX. WARNING - HARD CODED DATA!

      if ($q['id'] == 33 && $savedVar['104'] || $q['id'] == 35 && $savedVar['103'] || $q['id'] == 34 && $savedVar['102']) { 

      $result[$q['id']]['text'] = $q['text'];
      $result[$q['id']]['id'] = $q['id'];
      $result[$q['id']]['single'] = $q['single_answer'];
       if (strlen($q['hint_title']) > 1) {
        $result[$q['id']]['hint_title'] = $q['hint_title'];
        $result[$q['id']]['hint_text'] = $q['hint_text'];
        }
      $id = $q['id'];
      $answers = $sql->fetchAll("SELECT * FROM `nordstrom_quiz_answers` WHERE question_id = $id");
      foreach($answers as $key => $a) {
        $result[$q['id']]['answers'][$key]['id'] = $a['id'];
        $result[$q['id']]['answers'][$key]['text'] = $a['answer_text'];
      }
     } // ENDIF
    }
    echo json_encode($result);
    
    }
    
    public function getResultsAction() {

	$results = Mage::getSingleton('core/session')->getData('results'); 

	$ansId = $results['answers'];
	
	$sql = Mage::getSingleton('core/resource')->getConnection('core_read');


	if(  in_array( '102', $ansId) && !in_array( '103', $ansId) && !in_array( '104', $ansId) ) $regimen = "antiaging";
	if( !in_array( '102', $ansId) && !in_array( '103', $ansId) &&  in_array( '104', $ansId) ) $regimen = "acne";
	if( !in_array( '102', $ansId) &&  in_array( '103', $ansId) && !in_array( '104', $ansId) ) $regimen = "disc";
	if(  in_array( '102', $ansId) && !in_array( '103', $ansId) &&  in_array( '104', $ansId) ) $regimen = "antiaging-acne";
	if(  in_array( '102', $ansId) &&  in_array( '103', $ansId) && !in_array( '104', $ansId) ) $regimen = "antiaging-disc";
	if( !in_array( '102', $ansId) &&  in_array( '103', $ansId) &&  in_array( '104', $ansId) ) $regimen = "acne-disc";


       $results = Mage::getSingleton('core/session')->getData('results'); 
       $sql = Mage::getSingleton('core/resource')->getConnection('core_read');

		$i=0;
		foreach( $ansId as $id ){
		  	$ans_id =  $ans_id." answer_id = ".$id;
	  		$i++;
	  		if( $i < count($ansId) ) $ans_id = $ans_id." OR ";	
		}

        $attributes = $sql->fetchAll("SELECT attribute_id FROM `nordstrom_quiz_attributes` WHERE ".$ans_id." order by attribute_id ASC");
        foreach ($attributes as $attr) {
		if($attr['attribute_id'] > 99)$ids[] = $attr['attribute_id'];
        }

	    $ids = array_unique($ids);

if($regimen == "antiaging") $attr_names = array('exfoliator_benefits','moisturizer_benefits','spf_benefits','cleanser_benefits','serum_benefits','anti_aging_treatment','eye_cream_benefits'); //anti
if($regimen == "acne") $attr_names = array('cleanser_benefits','exfoliator_benefits','acne_treatment_benefits','serum_benefits','acne_treatment_benefits','moisturizer_benefits','eye_cream_benefits','spf_benefits'); //acne
if($regimen == "disc") $attr_names = array('exfoliator_benefits','discoloration_benefits','serum_benefits','spf_benefits','cleanser_benefits','moisturizer_benefits','discoloration_benefits','eye_cream_benefits'); //disc
if($regimen == "antiaging-acne") $attr_names = array('exfoliator_benefits','acne_treatment_benefits','spf_benefits','cleanser_benefits','serum_benefits','anti_aging_treatment','moisturizer_benefits','eye_cream_benefits'); //antiaging-acne
if($regimen == "antiaging-disc") $attr_names = array('exfoliator_benefits','spf_benefits','cleanser_benefits','discoloration_benefits','anti_aging_treatment','moisturizer_benefits','eye_cream_benefits'); //anti-disc
if($regimen == "acne-disc") $attr_names = array('exfoliator_benefits','spf_benefits','cleanser_benefits','serum_benefits','moisturizer_benefits','eye_cream_benefits'); //acne-disc  


$pos = 0;
foreach($attr_names as $x_attr_name){
$search_terms = array();
	foreach( $ids as $z_id ){
		$x_id = $z_id;
		$search_terms[] = array('like' => "%$x_id%");
	}
      $collection = "";
      $collection = Mage::getModel('catalog/product')->getCollection()->setStoreId(3);
      $collection->addAttributeToFilter(  $x_attr_name, $search_terms  );
      $collection->addAttributeToSelect('*');

        $s=array();
        $i=0;
        foreach ($collection as $product) {
           	$s[$i]['id'] = $product->getId();
           	$s[$i]['name'] = $product->getName();
           	$s[$i]['sku'] = $product->getSku();
      	 	//$s[$i]['price'] = $product->getPrice();
		$s[$i]['price'] = "$".number_format((float)$product->getPrice(), 0, '.', '');
      		$s[$i]['small_image'] = $product->getImageUrl();
		//$s[$i]['small_image'] = Mage::helper('catalog/image')->init($product, 'small_image')->resize(135);

           	$s[$i]['url_path'] = $product->getProductUrl();
  		$s[$i]['attr_name'] = $x_attr_name;

	    	$z_sku = explode("_", $product->getSku());
	    	$attributeModel = Mage::getModel('eav/entity_attribute')->loadByCode(4, $x_attr_name); 

	    	$attr_val = $sql->fetchOne("SELECT catalog_product_entity_varchar.`value` FROM catalog_product_entity INNER JOIN catalog_product_entity_varchar ON catalog_product_entity_varchar.entity_id = catalog_product_entity.entity_id WHERE catalog_product_entity.sku = ".$z_sku[0]." AND catalog_product_entity_varchar.attribute_id = ".$attributeModel->getAttributeId());


		
		$z_attr_val = explode(",", $attr_val);
		
		$result1 = array_intersect( $z_attr_val , $ids );
		$s[$i]['ranking'] = count($result1);
	     	$s[$i]['ranking_rest'] = $result1;
		$i++;
        }

	$ranking = array();
	foreach($s as $k => $d) {
 	 	$ranking[$k] = $d['ranking'];
	}


	array_multisort($ranking, SORT_DESC, $s);
	$z_prod_all[$x_attr_name] = $s;
	$z_prod_rec[$pos++] = $s[0];
	
	$z_add_prod = array();

	if($regimen == "antiaging"){
		$z_add_prod[1]['pos'] = 1;
		$z_add_prod[1]['sku'] = 10143;
		$z_add_prod[1]['attr_name'] = 'anti_aging_treatment';
		
		if(  !in_array( '113', $ansId) && !in_array( '114', $ansId)){
			$z_add_prod[2]['pos'] = 6;
			$z_add_prod[2]['sku'] = 10158;
			$z_add_prod[2]['attr_name'] = 'moisturizer_benefits';
		}
	}
	
	if($regimen == "acne"){
		$z_add_prod[2]['pos'] = 3;
		$z_add_prod[2]['sku'] = 10161;
		$z_add_prod[2]['attr_name'] = 'acne_treatment_benefits';
	}

	if($regimen == "disc"){
		$z_add_prod = array();
	}

	if($regimen == "antiaging-acne"){
		$z_add_prod[1]['pos'] = 1;
		$z_add_prod[1]['sku'] = 10143;
     		$z_add_prod[1]['attr_name'] = 'anti_aging_treatment';

		if(  !in_array( '113', $ansId) && !in_array( '114', $ansId)){
			$z_add_prod[2]['pos'] = 6;
			$z_add_prod[2]['sku'] = 10158;
			$z_add_prod[2]['attr_name'] = 'moisturizer_benefits';
		}
	}


	if($regimen == "antiaging-disc"){
		$z_add_prod[1]['pos'] = 1;
		$z_add_prod[1]['sku'] = 10143;
       	$z_add_prod[1]['attr_name'] = 'anti_aging_treatment';
		$z_add_prod[2]['pos'] = 2;
 		$z_add_prod[2]['sku'] = 10123;
		$z_add_prod[2]['attr_name'] = 'discoloration_benefits';
		
		if(  !in_array( '113', $ansId) && !in_array( '114', $ansId)){
			$z_add_prod[3]['pos'] = 6;
			$z_add_prod[3]['sku'] = 10158;
			$z_add_prod[3]['attr_name'] = 'moisturizer_benefits';
		}
	}


	if($regimen == "acne-disc"){ 
		$z_add_prod[1]['pos'] = 1;
 		$z_add_prod[1]['sku'] = 10123;
		$z_add_prod[1]['attr_name'] = 'discoloration_benefits';
		$z_add_prod[2]['pos'] = 2;
		$z_add_prod[2]['sku'] = 10000;
		$z_add_prod[2]['attr_name'] = 'acne_treatment_benefits';
		$z_add_prod[3]['pos'] = 6;
		$z_add_prod[3]['sku'] = 10161;
		$z_add_prod[3]['attr_name'] = 'acne_treatment_benefits';
	}




	foreach( $z_add_prod as $k => $l){
	     if($pos == $z_add_prod[$k]['pos']){	
              $_product = Mage::getModel('catalog/product')->loadByAttribute('sku', $z_add_prod[$k]['sku']  );
 
		$z_prod_rec[$pos]['id']   		= $_product->getId();
            $_product->setStoreId(3)->load( $_product->getId() );
           	$z_prod_rec[$pos]['name'] 		= $_product->getName();
           	$z_prod_rec[$pos]['sku']  		= $_product->getSku();
           	//$z_prod_rec[$pos]['price']  	= $_product->getPrice();
			$z_prod_rec[$pos]['price']		= "$".number_format((float)$_product->getPrice(), 0, '.', '');
           	$z_prod_rec[$pos]['small_image'] 	= $_product->getImageUrl();
			//$z_prod_rec[$pos]['small_image']   = Mage::helper('catalog/image')->init($_product, 'small_image')->resize(135);
          	$z_prod_rec[$pos]['url_path'] 	= $_product->getProductUrl();
		 	$z_prod_rec[$pos]['attr_name'] 	= $z_add_prod[$k]['attr_name'];
			$z_prod_rec[$pos++]['ranking'] 	= 'added';
	     }
	}

}


	foreach(array_keys($z_prod_rec) as $key) {
   		unset($z_prod_rec[$key]['ranking']);
   		//unset($z_prod_rec[$key]['attr_name']);
	}

	$z_get_prod = $this->searchDupProduct( $z_prod_rec, $z_prod_all );       
	Mage::getSingleton('core/session')->setData('savedResults', $z_get_prod);
    echo json_encode($z_get_prod);		
} // Function end



public function searchDupProduct( $array, $array_all ){
	$z_prod_rec = $array;
	$z_prod_all = $array_all;

	foreach( $z_prod_rec  as $k => $prod ){
		$z_prod_em[$k]['id'] = 	$z_prod_rec[$k]['id'];   		
       	$z_prod_em[$k]['name'] = 	$z_prod_rec[$k]['name']; 		
       	$z_prod_em[$k]['sku'] = 	$z_prod_rec[$k]['sku'];  		
	}

	$z_search_dup 	= array_map("unserialize", array_unique(array_map("serialize", $z_prod_em)));
	$z_result 		= array_diff_assoc($z_prod_rec, $z_search_dup);

	$i=0;
	foreach( $z_result as $key => $value){
		$x_pos_key = $key;
		$z_value[$i]   = $value;
		$z_value[$i]['arr_key'] = $key;
		$i++;
	}

	foreach( $z_value as $value){  
		foreach( $z_prod_all[$value['attr_name']] as $x_val  ){
			if( $value['id'] != $x_val['id']  ){
				$x_id = $this->searchForId( $x_val['id'], $z_prod_rec );
				if( $x_id == null ){
					$z_prod_rec[$value['arr_key']] = $x_val;
				break;
				}
			}	
		}
	}
	return $z_prod_rec;	
}


public function searchForId($id, $array) {
   foreach ($array as $key => $val) {
       if ($val['id'] === $id) {
           return $key;
       }
   }
   return null;
}


    public function getCustomerInfoAction() {
	$results = Mage::getSingleton('core/session')->getData('results');
	$z_store_ipad_name  = explode( "-", $_COOKIE["store_ipad_id"] );
	$date = new DateTime( $_COOKIE["current_ipad_date"] );
	$date->format('F d, Y'); 
	$z_get_customer = array();
	$z_get_customer['name_customer']	= ucwords(strtolower($results['name']));
	$z_get_customer['evaluation_name'] = "Kate Somerville Skin Evaluation";
	$z_get_customer['current_date'] 	= $date->format('F d, Y');
	$z_get_customer['nordstrom_store'] = ucwords(strtolower( $z_store_ipad_name[0] ));
     
     echo json_encode($z_get_customer);
    }

    public function checkEmailAction() {
    $email = file_get_contents("php://input");
    if ($email == "" ) { echo json_encode(array('status'=>'failed', 'error' => 'leading_zeroinemail')); die(); };
    $savedBefore = Mage::getSingleton('core/session')->getData('savedBefore');
    if (!$savedBefore) { echo json_encode(array('status'=>'failed', 'error' => 'leading_zero')); die(); }
    $results = Mage::getSingleton('core/session')->getData('savedResults');   
    
    $template = file_get_contents(Mage::getBaseDir() . "/nordstrom/template/email.html");
    $product_template = file_get_contents(Mage::getBaseDir() . "/nordstrom/template/product.html");
    // This will hold final template;
    $top_products = "";
    $full_regime = "";
    
    foreach ($results as $index => $res) {
      if ($index < 4) {
      $temp = $product_template;
        foreach ($res as $key => $value) {
          if ($key == 'small_image') { $value = str_replace("https", "https", $value); }
          $temp = str_replace('<% '.$key.' %>', $value, $temp);
        }
        $top_products .= $temp;
      } else {
        $temp = $product_template;
        foreach ($res as $key => $value) {
        if ($key == 'small_image') { $value = str_replace("https", "https", $value); }
          $temp = str_replace('<% '.$key.' %>', $value, $temp);
        }   
        $full_regime .= $temp;   
        
      }
      
    }
    $savedBefore = (array)$savedBefore;
    $z_store_ipad_name  = explode( "-", $_COOKIE["store_ipad_id"] );
    $date = new DateTime( $_COOKIE["current_ipad_date"] );
    $variables = array(
     'name' => $savedBefore['name'],
     'logo' => Mage::getBaseUrl() . 'nordstrom/img/logo.png',
     'title' => Mage::getBaseUrl() . 'nordstrom/img/title.png',
     'line' => Mage::getBaseUrl() . 'nordstrom/img/line.png',
     'url' => Mage::getBaseUrl(),
     'date' => $date->format('F d, Y'),
     'top_products' => $top_products,
     'full_regime'  => $full_regime,
     'store_ipad_id' => ucwords(strtolower($z_store_ipad_name[0])) 
    );
    foreach ($variables as $key => $value)
    {
        $template = str_replace('<% '.$key.' %>', $value, $template);
    }
  
    //$transport = Swift_SendmailTransport::newInstance('/usr/sbin/sendmail -bs');
  //  $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', 465, 'ssl')
 $transport = Swift_SmtpTransport::newInstance('smtp.gmail.com', '587', 'tls')
    ->setUsername('ksscsmtp1@gmail.com')
    ->setPassword('#ksscsmtp1_');
    // Uncomment to debug
    $mailer = Swift_Mailer::newInstance($transport);
    //$logger = new Swift_Plugins_Loggers_EchoLogger();
    //$mailer->registerPlugin(new Swift_Plugins_LoggerPlugin($logger));
     $z_store_ipad_name  = explode( "-", $_COOKIE["store_ipad_id"] );

    $emailFrom = "Kate Somerville Skincare";
	$message = Swift_Message::newInstance()
    ->setSubject('Your Skincare Evaluation Results')
    ->setFrom(array('web@katesomerville.com' =>  $emailFrom))
    ->setTo(array($email => $savedBefore['name']))
    ->setContentType('text/html')
    ->setBody($template);
    if ($mailer->send($message, $failures))
    {
	  $id = Mage::getSingleton('core/session')->getData('lastId');
	  $write = Mage::getSingleton('core/resource')->getConnection('core_write'); 
	  $query = "UPDATE  `nordstrom_quiz_completed` SET  `emailed` =  'Y' WHERE  `id` ={$id}";
      $write->query($query);
      echo json_encode(array('status'=>'sent'));
    }
    else
    {
      echo json_encode(array('status'=>'failed', 'error' => $failures));
    }
    
    
    }
    
    public function getPdfsAction() {
     $url = Mage::getBaseDir('media') . "/nordstrom_pdf/";
     $files = scandir($url);
     foreach($files as $key=>$file) {
          if (strpos($file, ".pdf") !== false) {
              $dot = strpos($file, ".");
              $pdfs[$key]['title'] = strstr($file, '.', true);
              $pdfs[$key]['url'] = Mage::getBaseUrl(Mage_Core_Model_Store::URL_TYPE_MEDIA) . "/nordstrom_pdf/" . $file;
          }
      }
     echo json_encode($pdfs);
    }

	public function printAction() {
	  $id = Mage::getSingleton('core/session')->getData('lastId');
	  $write = Mage::getSingleton('core/resource')->getConnection('core_write'); 
	  $query = "UPDATE  `nordstrom_quiz_completed` SET  `printed` =  'Y' WHERE  `id` ={$id}";
      $write->query($query);	
	}
    

}    
