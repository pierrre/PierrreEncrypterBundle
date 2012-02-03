# PierrreEncrypterBundle

PierrreEncrypterBundle provides easy to use encryption service in Symfony2.

## Features

- A service you can call from PHP code
- A Twig extension (optional)
- 100% unit testing! [![Build Status](https://secure.travis-ci.org/pierrre/PierrreEncrypterBundle.png)](http://travis-ci.org/pierrre/PierrreEncrypterBundle.png)

## Prerequisites

Make sure PHP Mcrypt extension is installed.

## Installation

This installation procedure is intended for use with Symfony2, but you can adapt it with submodules.

### Step 1: Download the bundle

Add the following lines in your `deps` file:

```
[PierrreEncrypterBundle]
    git=http://github.com/pierrre/PierrreEncrypterBundle.git
    target=/bundles/Pierrre/EncrypterBundle
```

Now, run the vendors script to download the bundle:

``` bash
php bin/vendors install
```

### Step 2: Configure the Autoloader

Add the Pierrre namespace to your autoloader:

``` php
<?php
// app/autoload.php
$loader->registerNamespaces(array(
    // ...
    'Pierrre' => __DIR__.'/../vendor/bundles',
));
```

### Step 3: Enable the bundle

Finally, enable the bundle in the kernel:

``` php
<?php
// app/AppKernel.php
public function registerBundles()
{
    $bundles = array(
        // ...
        new Pierrre\EncrypterBundle\PierrreEncrypterBundle(),
    );
}
```

## Configuration

This is the default configuration, all values are optional:

``` yaml
# app/config.yml
pierrre_encrypter:
    encrypters: #Encrypters list, requires at least one encrypter.
        my_encrypter: #Encrypter name
            key: "@kernel.secret" #The secret that is used to encrypt data. By default, it will use the kernel secret.
            algorithm: "rijndael-128" #Encryption algorithm
            mode: "cbc" #Encryption mode
            random_initialization_vector: true #If you set it to false, it will use a blank string as initialization vector.
            base64: true #Encode the encrypted data with the base64 algorithm.
            base64_url_safe: true #Replace "+" and "/" characters by "-" and "_".
    twig: #Twig extension
        enabled: false #Enable extension
        default_encrypter: null #Default encrypter. By default, it's the first encrypter
```

Please read the [Mcrypt documentation](http://www.php.net/manual/en/book.mcrypt.php).  

## Usage

Please note:

- If a random initialization vector is used, it will be concatenated at the beginning of the encrypted data.
- "\0" characters at the end of the decrypted data will be removed.
- "=" characters at the end of the encrypted+base64 data will be removed.

### Programmatically

#### With dependency injection container

``` php
<?php
$encrypter = $container->get('pierrre_encrypter.manager')->get('my_encrypter');
$data = 'foobar';
$encryptedData = $encrypter->encrypt($data);
$decryptedData = $encrypter->decrypt($encryptedData);
```

#### Manually

``` php
<?php
use Pierrre\EncrypterBundle\Util\Encrypter;
$encrypter = new Encrypter(array('key' => 'mySecret'));
```

### Twig extension

```
{{ data | encrypt }}
{{ data | encrypt('my_encrypter') }}
```