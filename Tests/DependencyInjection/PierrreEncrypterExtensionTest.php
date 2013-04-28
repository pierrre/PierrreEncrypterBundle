<?php

namespace Pierrre\EncrypterBundle\Tests\DependencyInjection;

use Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension;

use Symfony\Component\DependencyInjection\ContainerBuilder;

class PierrreEncrypterExtensionTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
     */
    public function testLoad()
    {
        $extension = new PierrreEncrypterExtension();
        $containerBuilder = $this->getContainerBuilder();

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
     * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
     */
    public function testLoadWithEncrypterKeyDefault()
    {
        $extension = new PierrreEncrypterExtension();
        $containerBuilder = $this->getContainerBuilder();

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
     * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
     */
    public function testLoadWithTwigDefaultEncrypterDefault()
    {
        $extension = new PierrreEncrypterExtension();
        $containerBuilder = $this->getContainerBuilder();

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
     * @expectedException Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     *
     * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
     */
    public function testLoadWithEncryptersEmpty()
    {
        $extension = new PierrreEncrypterExtension();
        $containerBuilder = $this->getContainerBuilder();

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
     * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
     */
    public function testLoadServiceEncrypterManager()
    {
        $extension = new PierrreEncrypterExtension();
        $containerBuilder = $this->getContainerBuilder();

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
     * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::load
     */
    public function testLoadServiceTwigExtension()
    {
        $extension = new PierrreEncrypterExtension();
        $containerBuilder = $this->getContainerBuilder();

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
        $this->assertInstanceOf('Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension', $twigExtension);
    }

    private function getContainerBuilder()
    {
        $containerBuilder = new ContainerBuilder();
        $containerBuilder->setParameter('kernel.secret', 'secret');

        return $containerBuilder;
    }

    /**
     * @covers Pierrre\EncrypterBundle\DependencyInjection\PierrreEncrypterExtension::getConfigTreeBuilder
     */
    public function testGetConfigTreeBuilder()
    {
        $extension = new PierrreEncrypterExtension();

        $treeBuilder = $extension->getConfigTreeBuilder();

        $this->assertInstanceOf('Symfony\Component\Config\Definition\Builder\TreeBuilder', $treeBuilder);
    }
}
