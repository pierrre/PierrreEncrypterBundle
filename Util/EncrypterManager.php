<?php

namespace Pierrre\EncrypterBundle\Util;

use \ArrayAccess;
use \Exception;

class EncrypterManager{
	private $encrypters;
	private $configs;
	
	/**
	 * @param array $configs
	 */
	public function __construct(array $configs = array()){
		$this->encrypters = array();
		$this->configs = $configs;
	}
	
	/**
	 * @param string $name
	 * @param Pierrre\EncrypterBundle\Util\Encrypter $encrypter
	 */
	public function add($name, Encrypter $encrypter){
		$this->encrypters[$name] = $encrypter;
	}
	
	/**
	 * @param string $name
	 * 
	 * @return Pierrre\EncrypterBundle\Util\Encrypter
	 */
	public function remove($name){
		if(isset($this->encrypters[$name])){
			$encrypter = $this->encrypters[$name];
			unset($this->encrypters[$name]);
		} else{
			$encrypter = null;
		}
		
		return $encrypter;
	}
	
	/**
	 * @param string $name
	 * 
	 * @return boolean
	 */
	public function has($name){
		return isset($this->encrypters[$name]) || isset($this->configs[$name]);
	}
	
	/**
	 * @param string $name
	 * 
	 * @return Pierrre\EncrypterBundle\Util\Encrypter
	 * 
	 * @throws \Exception
	 */
	public function get($name){
		if(isset($this->encrypters[$name])){
			$encrypter = $this->encrypters[$name];
		} else{
			if(isset($this->configs[$name])){
				$config = $this->configs[$name];
				$encrypter = new Encrypter($config);
				
				$this->encrypters[$name] = $encrypter;
			} else{
				throw new Exception('Unknown encrypter (' . $name . ')');
			}
		}
		
		return $encrypter;
	}
	
	/**
	 * @param mixed $name
	 */
	private function checkNameIsString($name){
		
	}
}