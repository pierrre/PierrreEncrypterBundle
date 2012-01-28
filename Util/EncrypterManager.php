<?php

namespace Pierrre\EncrypterBundle\Util;

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
	 * 
	 * @return boolean
	 */
	public function has($name){
		$this->checkNameIsString($name);
		
		return isset($this->configs[$name]);
	}
	
	/**
	 * @param string $name
	 * 
	 * @return Pierrre\EncrypterBundle\Util\Encrypter
	 * 
	 * @throws \Exception
	 */
	public function get($name){
		$this->checkNameIsString($name);
		
		if(isset($this->encrypters[$name])){
			$encrypter = $this->encrypters[$name];
		} else{
			if(isset($this->configs[$name])){
				$config = $this->configs[$name];
				$encrypter = new Encrypter($config);
				
				$this->encrypters[$name] = $encrypter;
			} else{
				throw new \InvalidArgumentException('Unknown encrypter (' . $name . ')');
			}
		}
		
		return $encrypter;
	}
	
	/**
	 * @param string $name
	 */
	private function checkNameIsString($name){
		if(!is_string($name)){
			throw new \InvalidArgumentException('Encrypter name is not a string');
		}
	}
}