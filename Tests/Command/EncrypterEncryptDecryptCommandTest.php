<?php

namespace Pierrre\EncrypterBundle\Tests\Command;

use Pierrre\EncrypterBundle\Command\EncrypterDecryptCommand;
use Pierrre\EncrypterBundle\Command\EncrypterEncryptCommand;
use Pierrre\EncrypterBundle\Util\EncrypterManager;

use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\Output;
use Symfony\Component\DependencyInjection\ContainerInterface;

class EncrypterEncryptDecryptCommandTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @dataProvider providerEncryptDecrypt
	 * 
	 * @covers Pierrre\EncrypterBundle\Command\EncrypterEncryptCommand
	 * @covers Pierrre\EncrypterBundle\Command\EncrypterDecryptCommand
	 */
	public function testEncryptDecrypt(ContainerInterface $container){
		$data = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';
		
		$encryptCommand = new EncrypterEncryptCommand();
		$encryptCommand->setContainer($container);
		$input = new ArrayInput(array(
			'encrypterName' => 'encrypter',
			'data' => $data
		));
		$output = new StringOutput();
		$encryptCommand->run($input, $output);
		$encryptedData = $output->getContent();
		
		$decryptCommand = new EncrypterDecryptCommand();
		$decryptCommand->setContainer($container);
		$input = new ArrayInput(array(
			'encrypterName' => 'encrypter',
			'encryptedData' => $encryptedData
		));
		$output = new StringOutput();
		$decryptCommand->run($input, $output);
		$decryptedData = $output->getContent();
		
		$this->assertEquals($data, $decryptedData);
	}
	
	public function providerEncryptDecrypt(){
		$configs = array(
			'encrypter' => array(
				'key' => 'secret'
			)
		);
		$encrypterManager = new EncrypterManager($configs);
		
		$container = $this->getMock('Symfony\Component\DependencyInjection\ContainerInterface');
		$container
		->expects($this->any())
		->method('get')
		->with('pierrre_encrypter.manager')
		->will($this->returnValue($encrypterManager));
		
		return array(
			array($container)
		);
	}
}

class StringOutput extends Output{
	private $content = '';
	
	public function getContent(){
		return $this->content;
	}
	
	public function clearContent(){
		$this->content = '';
	}
	
	/**
	 * @see Symfony\Component\Console\Output.Output::doWrite()
	 */
	public function doWrite($message, $newline){
		$this->content .= $message;
		
		if($newline){
			$this->content .= "\n";
		}
	}
}