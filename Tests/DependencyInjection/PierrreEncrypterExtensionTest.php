<?php

namespace Pierrre\EncrypterBundle\Tests\DependencyInjection;

use Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class PierrreEncrypterExtensionTest extends \PHPUnit_Framework_TestCase{
	/**
	 * @dataProvider providerLoad
	 * 
	 * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
	 */
	public function testLoad(PierrreEncrypterExtension $extension, ContainerBuilder $containerBuilder){
		$configs = array(
			array(
				'encrypters' => array(
					'encrypter' => array(
						'key' => 'secret',
						'algorithm' => MCRYPT_RIJNDAEL_128,
						'mode' => MCRYPT_MODE_CBC,
						'random_initialization_vector' => true,
						'base64' => true,
						'base64_url_safe' => true
					)
				),
				'twig' => array(
					'enabled' => true,
					'default_encrypter' => 'encrypter'
				)
			)
		);
		
		$extension->load($configs, $containerBuilder);
		
		$this->assertEquals($configs[0]['encrypters'], $containerBuilder->getParameter($extension->getAlias() . '.encrypters'));
		
		$this->assertEquals('encrypter', $containerBuilder->getParameter($extension->getAlias() . '.twig.default_encrypter'));
	}
	
	/**
	 * @dataProvider providerLoad
	 *
	 * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
	 */
	public function testLoadWithEncrypterKeyDefault(PierrreEncrypterExtension $extension, ContainerBuilder $containerBuilder){
		$configs = array(
			array(
				'encrypters' => array(
					'encrypter' => array(
						//No key defined
					)
				)
			)
		);
		
		$extension->load($configs, $containerBuilder);
		
		$parameterEncrypters = $containerBuilder->getParameter($extension->getAlias() . '.encrypters');
		$this->assertEquals($containerBuilder->getParameter('kernel.secret'), $parameterEncrypters['encrypter']['key']);
	}
	
	/**
	 * @dataProvider providerLoad
	 *
	 * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
	 */
	public function testLoadWithTwigDefaultEncrypterDefault(PierrreEncrypterExtension $extension, ContainerBuilder $containerBuilder){
		$configs = array(
			array(
				'encrypters' => array(
					'encrypter' => array()
				),
				'twig' => array(
					'enabled' => true
					//No default encrypter
				)
			)
		);
		
		$extension->load($configs, $containerBuilder);
		
		$this->assertEquals('encrypter', $containerBuilder->getParameter($extension->getAlias() . '.twig.default_encrypter'));
	}
	
	/**
	 * @dataProvider providerLoad
	 * 
	 * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
	 *
	 * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
	 */
	public function testLoadWithEncryptersEmpty(PierrreEncrypterExtension $extension, ContainerBuilder $containerBuilder){
		$configs = array(
			array(
				'encrypters' => array(
					//No encrypters
				)
			)
		);
		
		$extension->load($configs, $containerBuilder);
	}
	
	/**
	 * @dataProvider providerLoad
	 *
	 * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
	 */
	public function testLoadServiceEncrypterManager(PierrreEncrypterExtension $extension, ContainerBuilder $containerBuilder){
		$configs = array(
			array(
				'encrypters' => array(
					'encrypter' => array()
				)
			)
		);
		
		$extension->load($configs, $containerBuilder);
		
		$encrypterManager = $containerBuilder->get('pierrre_encrypter.manager');
		$this->assertInstanceOf('Pierrre\EncrypterBundle\Util\EncrypterManager', $encrypterManager);
		
		$encrypter = $encrypterManager->get('encrypter');
		$this->assertInstanceOf('Pierrre\EncrypterBundle\Util\Encrypter', $encrypter);
	}
	
	/**
	 * @dataProvider providerLoad
	 *
	 * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
	 */
	public function testLoadServiceTwigExtension(PierrreEncrypterExtension $extension, ContainerBuilder $containerBuilder){
		$configs = array(
			array(
				'encrypters' => array(
					'encrypter' => array()
				),
				'twig' => array(
					'enabled' => true
				)
			)
		);
		
		$extension->load($configs, $containerBuilder);
		
		$twigExtension = $containerBuilder->get('pierrre_encrypter.twig_extension');
		$this->assertInstanceOf('Pierrre\EncrypterBundle\Twig\Extension\Encrypter', $twigExtension);
	}
	
	public function providerLoad(){
		$extension = new PierrreEncrypterExtension();
		
		$containerBuilder = new ContainerBuilder();
		$containerBuilder->setParameter('kernel.secret', 'secret');
		
		return array(
			array($extension, $containerBuilder),
		);
	}
	
	public function testGetConfigTreeBuilder(){
		$extension = new PierrreEncrypterExtension();
		
		$treeBuilder = $extension->getConfigTreeBuilder();
		
		$this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $treeBuilder);
	}
}