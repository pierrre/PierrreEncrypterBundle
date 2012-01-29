<?php

namespace Pierrre\EncrypterBundle\Util;

class EncrypterManager{
	private $encrypters;
	private $configs;
	
	/**
	 * @param array $configs
	 * 
	 * @throws \InvalidArgumentException
	 */
	public function __construct(array $configs = array()){
		$this->encrypters = array();
		
		//Check configs name
		foreach($configs as $name => $config){
			$this->checkNameIsString($name);
			
			if(!is_array($config)){
				throw new \InvalidArgumentException('Encrypter config is not an array');
			}
		}
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
	 * @throws \InvalidArgumentException
	 */
	public function get($name){
		$this->checkNameIsString($name);
		
		if(isset($this->encrypters[$name])){
			$encrypter = $this->encrypters[$name];
		} else{
			if(isset($this->configs[$name])){
				$config = $this->configs[$name];
				$encrypter = new ManagedEncrypter($config);
				
				$this->encrypters[$name] = $encrypter;
			} else{
				throw new \InvalidArgumentException('Unknown encrypter (' . $name . ')');
			}
		}
		
		return $encrypter;
	}
	
	/**
	 * @param string $name
	 * 
	 * @throws \InvalidArgumentException
	 */
	private function checkNameIsString($name){
		if(!is_string($name)){
			throw new \InvalidArgumentException('Encrypter name is not a string');
		}
	}
}

class ManagedEncrypter extends Encrypter{
	/**
	 * @see Pierrre\EncrypterBundle\Util.Encrypter::close()
	 */
	public function close(){
		throw new \BadMethodCallException('close() method is not supported for a ManagedEncrypter');
	}
}