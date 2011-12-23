<?php

namespace Pierrre\EncrypterBundle\Twig\Extension;

use \Twig_Extension;
use \Twig_Filter_Method;

use Pierrre\EncrypterBundle\Util\Encrypter as Util_Encrypter;

class Encrypter extends Twig_Extension{
	/**
	 * @var Pierrre\EncrypterBundle\Util\Encrypter
	 */
	private $encrypter;

	/**
	 * @param Pierrre\EncrypterBundle\Util\Encrypter $encrypter
	 */
	public function __construct(Util_Encrypter $encrypter){
		$this->encrypter = $encrypter;
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
	public function encryptFilter($data){
		return $this->encrypter->encrypt($data);
	}
	
	/**
	 * @param string $encryptedData
	 */
	public function decryptFilter($encryptedData){
		return $this->encrypter->decrypt($encryptedData);
	}

	/**
	 * @see Twig_ExtensionInterface::getName()
	 */
	public function getName(){
		return 'encrypter_extension';
	}
}