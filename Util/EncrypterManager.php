<?php

namespace Pierrre\EncrypterBundle\Util;

use \ArrayAccess;

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
		return $this->get($name) != null;
	}
	
	/**
	 * @param string $name
	 * 
	 * @return Pierrre\EncrypterBundle\Util\Encrypter
	 * 
	 * @throws \Exception
	 */
	public function get($name){
		if(isset($this->encrypters['name'])){
			$encrypter = $this->encrypters['name'];
		} else{
			if(isset($this->config[$name])){
				$config = $this->config[$name];
				$encrypter = new Encrypter($config['key'], $config['mode'], $config['random_initialization_vector'], $config['base64'], $config['base64_url_safe']);
				
				$this->encrypters[$name] = $encrypter;
			} else{
				$encrypter = null;
			}
		}
		
		return $encrypter;
	}
}