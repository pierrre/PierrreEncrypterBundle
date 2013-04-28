<?php

namespace Pierrre\EncrypterBundle\Tests\Util;

use Pierrre\EncrypterBundle\Util\Encrypter;

class EncrypterTest extends \PHPUnit_Framework_TestCase
{
    const LOREM_IPSUM = 'Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat.';

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::__construct
     */
    public function testConstruct()
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::__construct
     */
    public function testConstructWithInitializationVectorRandom()
    {
        $options = self::getBaseOptions();
        $options['random_initialization_vector'] = true;
        $encrypter = new Encrypter($options);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::__construct
     */
    public function testConstructWithInitializationVectorFixed()
    {
        $options = self::getBaseOptions();
        $options['random_initialization_vector'] = false;
        $encrypter = new Encrypter($options);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::__construct
     */
    public function testConstructWithModeNotSupported()
    {
        $options = self::getBaseOptions();
        $options['mode'] = MCRYPT_MODE_STREAM;
        $encrypter = new Encrypter($options);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::__construct
     */
    public function testConstructWithAlgorithmInvalid()
    {
        $options = self::getBaseOptions();
        $options['algorithm'] = 'unknown algorithm';
        $encrypter = new Encrypter($options);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::__construct
     */
    public function testConstructWithModeInvalid()
    {
        $options = self::getBaseOptions();
        $options['mode'] = 'unknown mode';
        $encrypter = new Encrypter($options);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::__construct
     */
    public function testConstructWithKeyTooShort()
    {
        $options = self::getBaseOptions();
        $options['key'] = '';
        $encrypter = new Encrypter($options);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::__construct
     */
    public function testConstructWithKeyTooLong()
    {
        $options = self::getBaseOptions();
        $options['key'] = 'this key is too loooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooooong';
        $encrypter = new Encrypter($options);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::close
     */
    public function testClose()
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);

        $encrypter->close();
    }

    /**
     * @expectedException BadMethodCallException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::close
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::checkClosed
     */
    public function testCloseWithEncrypterAlreadyClosed()
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);

        $encrypter->close();

        $encrypter->close();
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecrypt()
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);

        $data = 'foobar';
        $encryptedData = $encrypter->encrypt($data);
        $decryptedData = $encrypter->decrypt($encryptedData);

        $this->assertEquals(Encrypter::convertToString($data), $decryptedData);
    }

    /**
     * @dataProvider providerSupportedDataType
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecryptWithSupportedDataType($data)
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);

        $encryptedData = $encrypter->encrypt($data);
        $decryptedData = $encrypter->decrypt($encryptedData);

        $this->assertEquals(Encrypter::convertToString($data), $decryptedData);
    }

    public function providerSupportedDataType()
    {
        return array(
            //String
            array('a'),
            array('azertyuiop'),
            array(self::LOREM_IPSUM),

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

    /**
     * @dataProvider providerAlgorithmMode
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecryptWithAlgorithmMode($algorithm, $mode)
    {
        $keySize = mcrypt_get_key_size($algorithm, $mode);
        $availableChars = '0123456789abcdefghijklmnopqrstuvwxyz';
        $availableCharsCount = strlen($availableChars);
        $key = '';
        for ($i = 0; $i < $keySize; $i++) {
            $key .= $availableChars[mt_rand(0, $availableCharsCount - 1)];
        }

        $options = array(
            'key' => $key,
            'algorithm' => $algorithm,
            'mode' => $mode
        );
        $encrypter = new Encrypter($options);

        $data = self::LOREM_IPSUM;

        $encryptedData = $encrypter->encrypt($data);
        $decryptedData = $encrypter->decrypt($encryptedData);

        $this->assertEquals(Encrypter::convertToString($data), $decryptedData);
    }

    public function providerAlgorithmMode()
    {
        $algorithms = mcrypt_list_algorithms();
        $modes = mcrypt_list_modes();

        $data = array();

        foreach ($algorithms as $algorithm) {
            foreach ($modes as $mode) {
                if ($mode != MCRYPT_MODE_STREAM && @mcrypt_get_key_size($algorithm, $mode) !== false) { //Test algorithm/mode availability
                    $data[] = array($algorithm, $mode);
                }
            }
        }

        return $data;
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecryptWithInitializationVectorRandom()
    {
        $options = self::getBaseOptions();
        $options['random_initialization_vector'] = true;
        $encrypter = new Encrypter($options);

        $data = 'foobar';

        $encryptedData1 = $encrypter->encrypt($data);
        $encryptedData2 = $encrypter->encrypt($data);

        $this->assertNotEquals($encryptedData1, $encryptedData2);

        $decryptedData1 = $encrypter->decrypt($encryptedData1);
        $decryptedData2 = $encrypter->decrypt($encryptedData2);

        $this->assertEquals($data, $decryptedData1);
        $this->assertEquals($data, $decryptedData2);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecryptWithInitializationVectorFixed()
    {
        $options = self::getBaseOptions();
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
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecryptWithBase64True()
    {
        $options = self::getBaseOptions();
        $options['base64'] = true;
        $encrypter = new Encrypter($options);

        $data = 'foobar';
        $encryptedData = $encrypter->encrypt($data);

        $this->assertNotContains($encryptedData, '=');

        $decryptedData = $encrypter->decrypt($encryptedData);

        $this->assertEquals(Encrypter::convertToString($data), $decryptedData);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecryptWithBase64False()
    {
        $options = self::getBaseOptions();
        $options['base64'] = false;
        $encrypter = new Encrypter($options);

        $data = self::LOREM_IPSUM;
        $encryptedData = $encrypter->encrypt($data);

        $this->assertNotRegExp('/^[A-Za-z0-9+\/]*$/', $encryptedData);
        $this->assertNotRegExp('/^[A-Za-z0-9-_]*$/', $encryptedData);

        $decryptedData = $encrypter->decrypt($encryptedData);

        $this->assertEquals(Encrypter::convertToString($data), $decryptedData);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecryptWithBase64UrlSafeTrue()
    {
        $options = self::getBaseOptions();
        $options['base64'] = true;
        $options['base64_url_safe'] = true;
        $encrypter = new Encrypter($options);

        $data = self::LOREM_IPSUM;
        $encryptedData = $encrypter->encrypt($data);

        $this->assertRegExp('/^[A-Za-z0-9-_]*$/', $encryptedData);

        $decryptedData = $encrypter->decrypt($encryptedData);

        $this->assertEquals(Encrypter::convertToString($data), $decryptedData);
    }

    /**
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testEncryptDecryptWithBase64UrlSafeFalse()
    {
        $options = self::getBaseOptions();
        $options['base64'] = true;
        $options['base64_url_safe'] = false;
        $encrypter = new Encrypter($options);

        $data = self::LOREM_IPSUM;
        $encryptedData = $encrypter->encrypt($data);

        $this->assertRegExp('/^[A-Za-z0-9+\/]*$/', $encryptedData);

        $decryptedData = $encrypter->decrypt($encryptedData);

        $this->assertEquals(Encrypter::convertToString($data), $decryptedData);
    }

    /**
     * @expectedException BadMethodCallException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::checkClosed
     */
    public function testEncryptWithEncrypterClosed()
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);

        $encrypter->close();

        $data = 'foobar';
        $encrypter->encrypt($data);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::encrypt
     */
    public function testEncryptWithEmptyData()
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);

        $data = '';
        $encrypter->encrypt($data);
    }

    /**
     * @expectedException BadMethodCallException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::checkClosed
     */
    public function testDecryptWithEncrypterClosed()
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);

        $data = 'foobar';
        $encryptedData = $encrypter->encrypt($data);

        $encrypter->close();

        $decryptedData = $encrypter->decrypt($encryptedData);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testDecryptWithEncryptedDataNotString()
    {
        $options = self::getBaseOptions();
        $encrypter = new Encrypter($options);

        $encryptedData = false;
        $decryptedData = $encrypter->decrypt($encryptedData);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testDecryptWithEncryptedDataInvalidBase64()
    {
        $options = self::getBaseOptions();
        $options['base64'] = true;
        $options['base64_url_safe'] = true;
        $encrypter = new Encrypter($options);

        $encryptedData = '&é(|)]';
        $decryptedData = $encrypter->decrypt($encryptedData);
    }

    /**
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::decrypt
     */
    public function testDecryptWithEncryptedDataNotLongEnoughToGetIV()
    {
        $options = self::getBaseOptions();
        $options['base64'] = false;
        $options['base64_url_safe'] = false;
        $options['random_initialization_vector'] = true;
        $encrypter = new Encrypter($options);

        $encryptedData = 'a';
        $decryptedData = $encrypter->decrypt($encryptedData);
    }

    /**
     * @dataProvider providerSupportedDataType
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::convertToString
     */
    public function testConvertToString($data)
    {
        $string = Encrypter::convertToString($data);

        $this->assertInternalType('string', $string);
    }

    /**
     * @dataProvider providerUnsupportedDataType
     *
     * @expectedException InvalidArgumentException
     *
     * @covers Pierrre\EncrypterBundle\Util\Encrypter::convertToString
     */
    public function testConvertToStringWithDataUnsupportedType($data)
    {
        $string = Encrypter::convertToString($data);
    }

    public function providerUnsupportedDataType()
    {
        return array(
            //Array
            array(array(1, 2 ,3)),

            //Object without __toString() method
            array(new ClassWithoutToStringMethod()),

            //Null
            array(null),
        );
    }

    public static function getBaseOptions()
    {
        return array(
            'key' => 'secret'
        );
    }
}

class ClassWithToStringMethod
{
    public function __toString()
    {
        return 'string representation of this object';
    }
}

class ClassWithoutToStringMethod
{
    //No __toString() method
}
