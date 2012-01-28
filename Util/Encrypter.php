<?php

namespace Pierrre\EncrypterBundle\Util;

class Encrypter{
	const FIXED_INITIALIZATION_VECTOR_CHAR = "\0";
	private static $BASE64_URL_SAFE_REPLACE = array('+/', '-_');
	
	const DEFAULT_ALGORITHM = MCRYPT_RIJNDAEL_128;
	const DEFAULT_MODE = MCRYPT_MODE_CBC;
	const DEFAULT_RANDOM_INITIALIZATION_VECTOR = true;
	const DEFAULT_BASE64 = true;
	const DEFAULT_BASE64_URL_SAFE = true;
	
	private $key;
	private $algorithm;
	private $mode;
	private $randomInitializationVector;
	private $base64;
	private $base64UrlSafe;
	
	private $module;
	private $initializationVectorSize;
	
	private $mcryptRandomMethod;
	private $fixedInitializationVector;
	
	/**
	 * @param array $options
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $options){
		//Options
		$this->key = isset($options['key']) ? (string)$options['key'] : '';
		$this->algorithm = isset($options['algorithm']) ? (string)$options['algorithm'] : self::DEFAULT_ALGORITHM;
		$this->mode = isset($options['mode']) ? (string)$options['mode'] : self::DEFAULT_MODE;
		$this->randomInitializationVector = isset($options['random_initialization_vector']) ? (bool)$options['random_initialization_vector'] : self::DEFAULT_RANDOM_INITIALIZATION_VECTOR;
		$this->base64 = isset($options['base64']) ? (bool)$options['base64'] : self::DEFAULT_BASE64;
		$this->base64UrlSafe = isset($options['base64_url_safe']) ? (bool)$options['base64_url_safe'] : self::DEFAULT_BASE64_URL_SAFE;
		
		//Initialize encryption
		try{
			$this->module = mcrypt_module_open($this->algorithm, '', $this->mode, '');
		} catch(\Exception $e){
			$this->module = false;
		}
		if($this->module === false){
			throw new \InvalidArgumentException('Unknown algorithm/mode');
		}
		
		if(strlen($this->key) == 0){
			throw new \InvalidArgumentException('The key length must be > 0');
		} else if(strlen($this->key) > ($keyMaxLength = mcrypt_enc_get_key_size($this->module))){
			throw new \InvalidArgumentException('The key length must be <= ' . $keyMaxLength . ' for the choosen algorithm (' . $this->algorithm . ')');
		}
		
		$this->initializationVectorSize = mcrypt_enc_get_iv_size($this->module);
		
		if($this->randomInitializationVector){
			$this->mcryptRandomMethod = defined('MCRYPT_DEV_URANDOM') ? MCRYPT_DEV_URANDOM : MCRYPT_DEV_RANDOM; 
		} else{
			$this->fixedInitializationVector = '';
			
			for($i = 0; $i < $this->initializationVectorSize; $i++){
				$this->fixedInitializationVector .= self::FIXED_INITIALIZATION_VECTOR_CHAR;
			}
		}
	}
	
	public function close(){
		$this->checkClosed();
		
		mcrypt_module_close($this->module);
		unset($this->module);
	}
	
	private function checkClosed(){
		if(!isset($this->module)){
			throw new \BadMethodCallException('The encrypter is closed');
		}
	}
	
	/**
	 * @param scalar|object $data
	 *
	 * @return string
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function encrypt($data){
		$this->checkClosed();
		
		$data = self::convertToString($data);
		
		if(strlen($data) == 0){
			throw new \InvalidArgumentException('Encryption doesn\'t support empty string');
		}
		
		//Encryption
		if($this->randomInitializationVector){
			$initializationVector = mcrypt_create_iv($this->initializationVectorSize, $this->mcryptRandomMethod);
		} else{
			$initializationVector = $this->fixedInitializationVector;
		}
		mcrypt_generic_init($this->module, $this->key, $initializationVector);
		$encryptedData = mcrypt_generic($this->module, (string)$data);
		if($this->randomInitializationVector){
			$encryptedData = $initializationVector . $encryptedData;
		}
		
		//Base64
		if($this->base64){
			$encryptedData = base64_encode($encryptedData);
			
			//Url safe
			if($this->base64UrlSafe){
				$encryptedData = strtr($encryptedData, self::$BASE64_URL_SAFE_REPLACE[0], self::$BASE64_URL_SAFE_REPLACE[1]);
			}
			
			$encryptedData = rtrim($encryptedData, '='); //Remove '=' at the end (it's useless)
		}
		
		return $encryptedData;
	}
	
	/**
	 * @param string $encryptedData
	 *
	 * @return string
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function decrypt($encryptedData){
		$this->checkClosed();
		
		//Check encrypted data
		if(!is_string($encryptedData)){
			throw new \InvalidArgumentException('Encrypted data must be a string');
		}
		
		//Base64
		if($this->base64){
			if($this->base64UrlSafe){
				$encryptedData = strtr($encryptedData, self::$BASE64_URL_SAFE_REPLACE[1], self::$BASE64_URL_SAFE_REPLACE[0]);
			}
			
			$encryptedData = base64_decode($encryptedData, true);
			
			if($encryptedData === false){
				throw new \InvalidArgumentException('Encrypted data is not a valid base64 string');
			}
		}
		
		//Encryption
		if($this->randomInitializationVector){
			$initializationVector = substr($encryptedData, 0, $this->initializationVectorSize);
			
			if(strlen($initializationVector) < $this->initializationVectorSize){
				throw new \InvalidArgumentException('Encrypted data is not long enough to get the initialization vector');
			}
			
			$encryptedData = substr($encryptedData, $this->initializationVectorSize);
		} else{
			$initializationVector = $this->fixedInitializationVector;
		}
		mcrypt_generic_init($this->module, $this->key, $initializationVector);
		$data = mdecrypt_generic($this->module, $encryptedData);
		$data = rtrim($data, "\0");
		
		return $data;
	}
	
	/**
	 * @param mixed $data
	 * 
	 * @return string
	 * 
	 * @throws \InvalidArgumentException
	 */
	public static function convertToString($data){
		if(is_string($data)){
			//Nothing
		} else if(is_int($data) || is_float($data)){
			$data = (string)$data;
		} else if(is_bool($data)){
			$data = $data ? '1' : '0';
		} else if(is_object($data)){
			if(method_exists($data, '__toString')){
				$data = (string)$data;
			} else{
				throw new \InvalidArgumentException('_toString() method doesn\'t exist for the "data" object');
			}
		} else{
			throw new \InvalidArgumentException('Encryption is not supported for the "' . gettype($data) . '" type');
		}
		
		return $data;
	}
}