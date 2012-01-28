<?php

namespace Pierrre\EncrypterBundle\Tests\Util;

use Pierrre\EncrypterBundle\Util\Encrypter;
use Pierrre\EncrypterBundle\Util\EncrypterManager;

class EncrypterManagerTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::__construct
	 */
	public function testConstruct(){
		$configs = array(
			'encrypter' => self::getEncrypterBaseOptions(),
		);
		$manager = new EncrypterManager($configs);
	}
	
	public function testConstructWithConfigsDefault(){
		$manager = new EncrypterManager();
	}
	
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::has
	 */
	public function testHas(){
		$configs = array(
			'encrypter' => self::getEncrypterBaseOptions(),
		);
		$manager = new EncrypterManager($configs);
		
		$this->assertTrue($manager->has('encrypter'));
		$this->assertFalse($manager->has('unknown_encrypter'));
	}
	
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::has
	 */
	public function testHasWithEncrypterUnknown(){
		$manager = new EncrypterManager();
		
		$this->assertFalse($manager->has('unknown_encrypter'));
	}
	
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::get
	 */
	public function testGet(){
		$configs = array(
			'encrypter' => self::getEncrypterBaseOptions(),
		);
		$manager = new EncrypterManager($configs);
		
		$encrypter = $manager->get('encrypter');
		
		$this->assertInstanceOf('Pierrre\EncrypterBundle\Util\Encrypter', $encrypter);
	}
	
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::get
	 */
	public function testGetWithEncrypterInCache(){
		$configs = array(
			'encrypter' => self::getEncrypterBaseOptions(),
		);
		$manager = new EncrypterManager($configs);
	
		$encrypter = $manager->get('encrypter');
		$encrypter = $manager->get('encrypter');
	
		$this->assertInstanceOf('Pierrre\EncrypterBundle\Util\Encrypter', $encrypter);
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * 
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::get
	 */
	public function testGetWithEncrypterUnknown(){
		$manager = new EncrypterManager();
		
		$encrypter = $manager->get('unknown_encrypter');
	}
	
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::checkNameIsString
	 */
	public function testCheckNameIsString(){
		$manager = new EncrypterManager();
	
		$manager->has('encrypter');
	}
	
	/**
	 * @expectedException InvalidArgumentException
	 * 
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::checkNameIsString
	 */
	public function testCheckNameIsStringWithNameNotString(){
		$manager = new EncrypterManager();
	
		$manager->has(new \stdClass());
	}
	
	private static function getEncrypterBaseOptions(){
		return EncrypterTest::getBaseOptions();
	}
}