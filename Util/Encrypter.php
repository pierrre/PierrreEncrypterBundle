<?php

namespace Pierrre\EncrypterBundle\Util;

class Encrypter{
	private static $URL_SAFE_REPLACE = array('+/', '-_');
	
	private $key;
	private $cipher;
	private $mode;

	public function __construct($key, $cipher, $mode){
		$this->key = $key;
		$this->cipher = $cipher;
		$this->mode = $mode;
	}

	/**
	 * @param string $data
	 *
	 * @return string
	 */
	public function encrypt($data){
		$encryptedData = mcrypt_encrypt($this->cipher, $this->key, (string)$data, $this->mode);
		$encryptedData = base64_encode($encryptedData);
		$encryptedData = strtr($encryptedData, self::$URL_SAFE_REPLACE[0], self::$URL_SAFE_REPLACE[1]);
		$encryptedData = str_replace('=', '', $encryptedData);
		
		return $encryptedData;
	}

	/**
	 * @param string $encryptedData
	 *
	 * @return string
	 */
	public function decrypt($encryptedData){
		$data = strtr($encryptedData, self::$URL_SAFE_REPLACE[1], self::$URL_SAFE_REPLACE[0]);
		$data = base64_decode($data);
		$data = mcrypt_decrypt($this->cipher, $this->key, $data, $this->mode);
		$data = rtrim($data, "\0");
		
		return $data;
	}
}