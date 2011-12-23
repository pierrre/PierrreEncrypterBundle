<?php

namespace Pierrre\EncrypterBundle\Util;

class Encrypter{
	const FIXED_INITIALIZATION_VECTOR_CHAR = "\0";
	private static $BASE64_URL_SAFE_REPLACE = array('+/', '-_');
	
	const DEFAULT_ALGORITHM = MCRYPT_RIJNDAEL_128;
	const DEFAULT_MODE = MCRYPT_MODE_CBC;
	const DEFAULT_USE_RANDOM_INITIALIZATION_VECTOR = true;
	const DEFAULT_USE_BASE64 = true;
	const DEFAULT_USE_BASE64_URL_SAFE = true;
	
	private $key;
	private $algorithm;
	private $mode;
	private $useRandomInitializationVector;
	private $useBase64;
	private $useBase64UrlSafe;
	
	private $module;
	private $initializationVectorSize;
	
	/**
	 * @param string $key
	 * @param string $algorithm
	 * @param string $mode
	 * @param boolean $useRandomInitializationVector
	 * @param boolean $useBase64
	 * @param boolean $useBase64UrlSafe
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function __construct($key,
			$algorithm = self::DEFAULT_ALGORITHM,
			$mode = self::DEFAULT_MODE,
			$useRandomInitializationVector = self::DEFAULT_USE_RANDOM_INITIALIZATION_VECTOR,
			$useBase64 = self::DEFAULT_USE_BASE64,
			$useBase64UrlSafe = self::DEFAULT_USE_BASE64_URL_SAFE){
		$this->key = (string)$key;
		$this->algorithm = (string)$algorithm;
		$this->mode = (string)$mode;
		$this->useRandomInitializationVector = (bool)$useRandomInitializationVector;
		$this->useBase64 = (bool)$useBase64;
		$this->useBase64UrlSafe = (bool)$useBase64UrlSafe;
		
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
	}
	
	/**
	 * @param string|mixed $data
	 *
	 * @return string
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function encrypt($data){
		//Convert data to string
		if(!is_string($data)){
			if(is_scalar($data)){
				$data = (string)$data;
			} else if(is_object($data)){
				if(method_exists($data, '__toString')){
					$data = (string)$data;
				} else{
					throw new \InvalidArgumentException('_toString() method doesn\'t exist for the "data" object');
				}
			} else{
				throw new \InvalidArgumentException('Encryption is not supported for the "' . gettype($data) . '" type');
			}
		}
		
		//Encryption
		if($this->useRandomInitializationVector){
			$initializationVector = mcrypt_create_iv($this->initializationVectorSize);
		} else{
			$initializationVector = $this->createFixedInitializationVector($this->initializationVectorSize);
		}
		mcrypt_generic_init($this->module, $this->key, $initializationVector);
		$encryptedData = mcrypt_generic($this->module, (string)$data);
		if($this->useRandomInitializationVector){
			$encryptedData = $initializationVector . $encryptedData;
		}
		
		//Base64
		if($this->useBase64){
			$encryptedData = base64_encode($encryptedData);
			
			//Url safe
			if($this->useBase64UrlSafe){
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
		//Check encrypted data
		if(!is_string($encryptedData)){
			throw new \InvalidArgumentException('Encrypted data must be a string');
		}
		
		//Base64
		if($this->useBase64){
			if($this->useBase64UrlSafe){
				$encryptedData = strtr($encryptedData, self::$BASE64_URL_SAFE_REPLACE[1], self::$BASE64_URL_SAFE_REPLACE[0]);
			}
			
			$encryptedData = base64_decode($encryptedData);
			
			if($encryptedData === false){
				throw new \InvalidArgumentException('Encrypted data is not a valid base64 string');
			}
		}
		
		//Encryption
		if($this->useRandomInitializationVector){
			$initializationVector = substr($encryptedData, 0, $this->initializationVectorSize);
			$encryptedData = substr($encryptedData, $this->initializationVectorSize);
		} else{
			$initializationVector = $this->createFixedInitializationVector($this->initializationVectorSize);
		}
		mcrypt_generic_init($this->module, $this->key, $initializationVector);
		$data = mdecrypt_generic($this->module, $encryptedData);
		$data = rtrim($data, "\0");
		
		return $data;
	}
	
	/**
	 * @param int $size
	 */
	private function createFixedInitializationVector($size){
		$initializationVector = '';
		
		for($i = 0; $i < $size; $i++){
			$initializationVector .= self::FIXED_INITIALIZATION_VECTOR_CHAR;
		}
		
		return $initializationVector;
	}
}