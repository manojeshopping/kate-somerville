<?php
/**
 * Configuration source class. Returns an array of
 * image types to be selected for the type sent 
 * in the product feed.
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Model_Source_Productfeed_Imagetype{
	/**
	 * Return the array of images
	 * 
	 * @return array
	 */
	public function toOptionArray()
	{
		$options	=	array(
			array(
				'value'	=>	'image',
				'label'	=>	'Full Image'
			),
			array(
				'value'	=>	'small_image',
				'label'	=>	'Small Image'
			),
			array(
				'value'	=>	'thumbnail',
				'label'	=>	'Thumbnail Image'
			),
		);
		
		return $options;
	}
}
?>
