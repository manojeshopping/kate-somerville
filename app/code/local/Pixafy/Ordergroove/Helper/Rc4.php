<?php
/**
 * RC4 encryption class used to decrypt / encrypt tokenized values
 * passed between Magento and Ordergroove
 * 
 * @package Pixafy_Ordergroove
 * @author Jason Alpert <jalpert@pixafy.com>
 */
class Pixafy_Ordergroove_Helper_Rc4 extends Mage_Core_Helper_Abstract{
	
	/**
	 * RC4 encryption types
	 */
	const RC4_TYPE_DECRYPT	=	'de';
	const RC4_TYPE_ENCRYPT	=	'en';
	
	/**
	 * Hash key used for decryption
	 * 
	 * @var string
	 */
	protected $_hashKey		=	'';
	
	/**
	 * Return the hash key
	 * 
	 * @return string
	 */
	private function getHashKey(){
		if(!$this->_hashKey){
			$this->_hashKey	=	Mage::helper('ordergroove/config')->getHashKey();
		}
		return $this->_hashKey;
	}
	
	/**
	 * Encrypt a value using the RC4 function
	 * 
	 * @param $value string
	 * @return string
	 */
	public function encrypt($value){
		return $this->rc4($this->getHashKey(), $value, self::RC4_TYPE_ENCRYPT);
	}
	
	/**
	 * Decrypt a value using the RC4 function
	 * 
	 * @param $value string
	 * @return string
	 */
	public function decrypt($value){
		return trim($this->rc4($this->getHashKey(), $value, self::RC4_TYPE_DECRYPT));
	}
	
	/**
	 * The raw function that performs the encryption / decryption of a string
	 * 
	 * @param string $key
	 * @param string $pt
	 * @param string $type
	 */
	protected function rc4($key, $pt, $type){
		if($type == 'de'){
			$pt = base64_decode($pt);
		}
		
		$s = array();
		for ($i=0; $i<256; $i++) { $s[$i] = $i; }

		$j = 0;
		$x;
		for ($i=0; $i<256; $i++) {
			$j = ($j + $s[$i] + ord($key[$i % strlen($key)])) % 256;
			$x = $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $x;
		}
		$i = 0;
		$j = 0;
		$ct = '';
		$y;
		for ($y=0; $y<strlen($pt); $y++) {
			$i = ($i + 1) % 256;
			$j = ($j + $s[$i]) % 256;
			$x =	 $s[$i];
			$s[$i] = $s[$j];
			$s[$j] = $x;
			$ct .= $pt[$y] ^ chr($s[($s[$i] + $s[$j]) % 256]);
		}
		if ($type == 'en'){
			$ct = base64_encode($ct);
		}
		return $ct;
	}
}
?>
