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
	
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::__construct
	 */
	public function testConstructWithoutConfigs(){
		$manager = new EncrypterManager();
	}
	
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::add
	 */
	public function testAdd(){
		$manager = new EncrypterManager();
		
		$encrypter = self::createBasicEncrypter();
		$manager->add('encrypter', $encrypter);
		
		$this->assertTrue($manager->has('encrypter'));
	}
	
	/**
	 * @covers Pierrre\EncrypterBundle\Util\EncrypterManager::remove
	 */
	public function testRemove(){
		$manager = new EncrypterManager();
		
		$encrypter = self::createBasicEncrypter();
		$manager->add('encrypter', $encrypter);
		
		$encrypterRemoved = $manager->remove('encrypter');
		
		$this->assertEquals($encrypter, $encrypterRemoved);
		
		$this->assertFalse($manager->has('encrypter'));
	}
	
	private static function createBasicEncrypter(){
		return new Encrypter(self::getEncrypterBaseOptions());
	}
	
	private static function getEncrypterBaseOptions(){
		return EncrypterTest::getBaseOptions();
	}
}