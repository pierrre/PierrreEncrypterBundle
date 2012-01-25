<?php

namespace Pierrre\EncrypterBundle\Tests\Util;

use Pierrre\EncrypterBundle\Util\Encrypter;

class EncrypterTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @param mixed $data
	 * 
	 * @dataProvider supportedTypeProvider
	 */
	public function testSupportedType($data){
		$options = $this->getOptions();
		$encrypter = new Encrypter($options);
		
		$encryptedData = $encrypter->encrypt($data);
		$decryptedData = $encrypter->decrypt($encryptedData);
		
		$encrypter->close();
		
		$this->assertEquals(Encrypter::convertToString($data), $decryptedData);
	}
	
	/**
	 * @param mixed $data
	 *
	 * @dataProvider unsupportedTypeProvider
	 * 
	 * @expectedException InvalidArgumentException
	 */
	public function testUnsupportedType($data){
		$options = $this->getOptions();
		$encrypter = new Encrypter($options);
	
		$encryptedData = $encrypter->encrypt($data);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testWithInvalidAlgorithm(){
		$options = $this->getOptions();
		$options['algorithm'] = 'unknown algorithm';
		$encrypter = new Encrypter($options);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testWithInvalidMode(){
		$options = $this->getOptions();
		$options['mode'] = 'unknown mode';
		$encrypter = new Encrypter($options);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testWithKeyTooShort(){
		$options = array(
			'key' => ''
		);
		$encrypter = new Encrypter($options);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testWithKeyTooLong(){
		$options = array(
			'key' => 'this key is too loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong'
		);
		$encrypter = new Encrypter($options);
	}
	
	public function testWithRandomInitializationVector(){
		$options = $this->getOptions();
		$options['random_initialization_vector'] = true;
		$encrypter = new Encrypter($options);
		
		$data = 'foobar';
		$encryptedData1 = $encrypter->encrypt($data);
		$encryptedData2 = $encrypter->encrypt($data);
		
		$this->assertNotEquals($encryptedData1, $encryptedData2);
	}
	
	public function testWithFixedInitializationVector(){
		$options = $this->getOptions();
		$options['random_initialization_vector'] = false;
		$encrypter = new Encrypter($options);
		
		$data = 'foobar';
		$encryptedData1 = $encrypter->encrypt($data);
		$encryptedData2 = $encrypter->encrypt($data);
		
		$this->assertEquals($encryptedData1, $encryptedData2);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testWithEmptyData(){
		$options = $this->getOptions();
		$encrypter = new Encrypter($options);
		
		$data = '';
		$encrypter->encrypt($data);
	}
	
	public function supportedTypeProvider(){
		return array(
			//String
			array('a'),
			array('azertyuiop'),
			
			//Boolean
			array(true),
			array(false),
			
			//Integer
			array(0),
			array(3),
			array(-6),
			
			//Float
			array(0.0),
			array(3.7),
			array(-7.3),
			
			//Object
			array(new ClassWithToStringMethod()),
		);
	}
	
	public function unsupportedTypeProvider(){
		return array(
			//Array
			array(array(1, 2 ,3)),
			
			//Object without __toString() method
			array(new ClassWithoutToStringMethod()),
			
			//Null
			array(null),
		);
	}
	
	protected function getOptions(){
		return array(
			'key' => 'foobar'
		);
	}
}

class ClassWithToStringMethod{
	public function __toString(){
		return 'string representation of this object';
	}
}

class ClassWithoutToStringMethod{
	//No __toString() method
}