<?php

namespace Pierrre\EncrypterBundle\Tests\Util;

use Pierrre\EncrypterBundle\Util\Encrypter;

class EncrypterTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructWithInvalidAlgorithm(){
		$options = $this->getOptions();
		$options['algorithm'] = 'unknown algorithm';
		$encrypter = new Encrypter($options);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructWithInvalidMode(){
		$options = $this->getOptions();
		$options['mode'] = 'unknown mode';
		$encrypter = new Encrypter($options);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructWithKeyTooShort(){
		$options = array(
			'key' => ''
		);
		$encrypter = new Encrypter($options);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testConstructWithKeyTooLong(){
		$options = array(
			'key' => 'this key is too loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong'
		);
		$encrypter = new Encrypter($options);
	}
	
	/**
	 * @param mixed $data
	 * 
	 * @dataProvider supportedDataTypeProvider
	 */
	public function testEncryptDecryptWithSupportedDataType($data){
		$options = $this->getOptions();
		$encrypter = new Encrypter($options);
		
		$encryptedData = $encrypter->encrypt($data);
		$decryptedData = $encrypter->decrypt($encryptedData);
		
		$encrypter->close();
		
		$this->assertEquals(Encrypter::convertToString($data), $decryptedData);
	}
	
	public function testEncryptDecryptWithRandomInitializationVector(){
		$options = $this->getOptions();
		$options['random_initialization_vector'] = true;
		$encrypter = new Encrypter($options);
	
		$data = 'foobar';
		
		$encryptedData1 = $encrypter->encrypt($data);
		$encryptedData2 = $encrypter->encrypt($data);
		
		$this->assertNotEquals($encryptedData1, $encryptedData2);
		
		$decryptedData1 = $encrypter->decrypt($encryptedData1);
		$decryptedData2 = $encrypter->decrypt($encryptedData2);
		
		$this->assertEquals($data, $decryptedData1);
		$this->assertEquals($decryptedData1, $decryptedData2);
	}
	
	public function testEncryptDecryptWithFixedInitializationVector(){
		$options = $this->getOptions();
		$options['random_initialization_vector'] = false;
		$encrypter = new Encrypter($options);
	
		$data = 'foobar';
		$encryptedData1 = $encrypter->encrypt($data);
		$encryptedData2 = $encrypter->encrypt($data);
	
		$this->assertEquals($encryptedData1, $encryptedData2);
		
		$decryptedData1 = $encrypter->decrypt($encryptedData1);
		$decryptedData2 = $encrypter->decrypt($encryptedData2);
		
		$this->assertEquals($data, $decryptedData1);
		$this->assertEquals($decryptedData1, $decryptedData2);
	}
	
	/**
	 * @param mixed $data
	 *
	 * @dataProvider unsupportedDataTypeProvider
	 * 
	 * @expectedException InvalidArgumentException
	 */
	public function testEncryptWithUnsupportedDataType($data){
		$options = $this->getOptions();
		$encrypter = new Encrypter($options);
	
		$encryptedData = $encrypter->encrypt($data);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testEncryptWithEmptyData(){
		$options = $this->getOptions();
		$encrypter = new Encrypter($options);
		
		$data = '';
		$encrypter->encrypt($data);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testDecryptWithEncryptedDataNotString(){
		$options = $this->getOptions();
		$encrypter = new Encrypter($options);
		
		$encryptedData = false;
		$encrypter->decrypt($encryptedData);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testDecryptWithEncryptedDataInvalidBase64(){
		$options = $this->getOptions();
		$options['base64'] = true;
		$options['base64_url_safe'] = true;
		$encrypter = new Encrypter($options);
	
		$encryptedData = '&é(|)]';
		$encrypter->decrypt($encryptedData);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 */
	public function testDecryptWithEncryptedDataNotLongEnoughToGetIV(){
		$options = $this->getOptions();
		$options['base64'] = false;
		$options['base64_url_safe'] = false;
		$options['random_initialization_vector'] = true;
		$encrypter = new Encrypter($options);
		
		$encryptedData = 'a';
		$encrypter->decrypt($encryptedData);
	}
	
	public function supportedDataTypeProvider(){
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
	
	public function unsupportedDataTypeProvider(){
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