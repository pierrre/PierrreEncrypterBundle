<?php

namespace Pierrre\EncrypterBundle\Twig\Extension;

use \Twig_Extension;
use \Twig_Filter_Method;

use Pierrre\EncrypterBundle\Util\EncrypterManager;

class Encrypter extends Twig_Extension{
	/**
	 * @var Pierrre\EncrypterBundle\Util\EncrypterManager
	 */
	private $encrypterManager;
	
	/**
	 * @var string
	 */
	private $defaultEncrypterName;
	
	/**
	 * @param Pierrre\EncrypterBundle\Util\EncrypterManager $encrypterManager
	 */
	public function __construct(EncrypterManager $encrypterManager, $defaultEncrypterName){
		$this->encrypterManager = $encrypterManager;
		$this->defaultEncrypterName = $defaultEncrypterName;
	}
	
	/**
	 * @see Twig_Extension::getFilters()
	 */
	public function getFilters(){
		return array(
			'encrypt' => new Twig_Filter_Method($this, 'encryptFilter'),
			'decrypt' => new Twig_Filter_Method($this, 'decryptFilter')
		);
	}
	
	/**
	 * @param string $data
	 */
	public function encryptFilter($data, $encrypterName = null){
		if($encrypterName == null){
			$encrypterName = $this->defaultEncrypterName;
		}
		
		return $this->encrypterManager->get($encrypterName)->encrypt($data);
	}
	
	/**
	 * @param string $encryptedData
	 */
	public function decryptFilter($encryptedData, $encrypterName = null){
		if($encrypterName == null){
			$encrypterName = $this->defaultEncrypterName;
		}
		
		return $this->encrypterManager->get($encrypterName)->decrypt($encryptedData);
	}
	
	/**
	 * @see Twig_ExtensionInterface::getName()
	 */
	public function getName(){
		return 'encrypter_extension';
	}
}