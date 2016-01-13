<?php

namespace Pierrre\EncrypterBundle\Twig\Extension;

use \Twig_Extension;
use \Twig_SimpleFilter;

use Pierrre\EncrypterBundle\Util\EncrypterManager;

class EncrypterExtension extends Twig_Extension
{
    /**
     * @var Pierrre\EncrypterBundle\Util\EncrypterManager
     */
    private $encrypterManager;

    /**
     * @var string
     */
    private $defaultEncrypterName;

    /**
     * @param Pierrre\EncrypterBundle\Util\EncrypterManager $encrypterManager
     */
    public function __construct(EncrypterManager $encrypterManager, $defaultEncrypterName)
    {
        $this->encrypterManager = $encrypterManager;

        if ($this->encrypterManager->has($defaultEncrypterName)) {
            $this->defaultEncrypterName = $defaultEncrypterName;
        } else {
            throw new \InvalidArgumentException('Unknown default encrypter');
        }
    }

    /**
     * @see Twig_Extension::getFilters()
     */
    public function getFilters()
    {
        return array(
            new Twig_SimpleFilter('encrypt', array($this, 'encryptFilter')),
            new Twig_SimpleFilter('decrypt', array($this, 'decryptFilter')),
        );
    }

    /**
     * @param string $data
     * @param string $encrypterName
     */
    public function encryptFilter($data, $encrypterName = null)
    {
        if ($encrypterName == null) {
            $encrypterName = $this->defaultEncrypterName;
        }

        return $this->encrypterManager->get($encrypterName)->encrypt($data);
    }

    /**
     * @param string $encryptedData
     * @param string $encrypterName
     */
    public function decryptFilter($encryptedData, $encrypterName = null)
    {
        if ($encrypterName == null) {
            $encrypterName = $this->defaultEncrypterName;
        }

        return $this->encrypterManager->get($encrypterName)->decrypt($encryptedData);
    }

    /**
     * @see Twig_ExtensionInterface::getName()
     */
    public function getName()
    {
        return 'pierrre_encrypter';
    }
}
