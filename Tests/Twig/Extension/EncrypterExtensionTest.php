<?php

namespace Pierrre\EncrypterBundle\Tests\Twig\Extension;

use Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension;
use Pierrre\EncrypterBundle\Util\EncrypterManager;

class EncrypterExtensionTest extends \PHPUnit_Framework_TestCase
{
    private $extension;

    /**
     * @covers Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension::__construct
     */
    public function testConstruct()
    {
        $extension = $this->getExtension();
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension::__construct
     */
    public function testConstructWithDefaultEncrypterUnknown()
    {
        $extension = $this->getExtension('unknown_encrypter');
    }

    /**
     * @covers Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension::getFilters
     */
    public function testGetFilter()
    {
        $extension = $this->getExtension();

        $filters = $extension->getFilters();
    }

    /**
     * @covers Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension::encryptFilter
     * @covers Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension::decryptFilter
     */
    public function testEncryptDecryptFilter()
    {
        $extension = $this->getExtension();
        $encrypterName = 'encrypter';
        $data = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $encryptedData = $extension->encryptFilter($data, $encrypterName);
        $decryptedData = $extension->decryptFilter($encryptedData, $encrypterName);

        $this->assertEquals($data, $decryptedData);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension::encryptFilter
     * @covers Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension::decryptFilter
     */
    public function testEncryptDecryptFilterWithEncrypterDefault()
    {
        $extension = $this->getExtension();
        $data = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua.';

        $encryptedData = $extension->encryptFilter($data);
        $decryptedData = $extension->decryptFilter($encryptedData);

        $this->assertEquals($data, $decryptedData);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Twig\Extension\EncrypterExtension::getName
     */
    public function testGetName()
    {
        $extension = $this->getExtension();

        $name = $extension->getName();
    }

    private function getExtension($defaultEncrypterName = 'encrypter')
    {
        $configs = array(
            'encrypter' => array(
                'key' => 'secret'
            )
        );
        $encrypterManager = new EncrypterManager($configs);

        $extension = new EncrypterExtension($encrypterManager, $defaultEncrypterName);

        return $extension;
    }
}
