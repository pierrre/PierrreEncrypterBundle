# PierrreEncrypterBundle

PierrreEncrypterBundle provides easy to use encryption in Symfony2.

Features include:

- A service you can call from PHP code
- A Twig extension (optional)

## Prerequisites

Make sure PHP Mcrypt extension is installed.

## Installation

This installation procedure is intended for use with Symfony2, but you can adapt it with submodules.

### Step 1: Download the bundle

Add the following lines in your `deps` file:

```
[PierrreEncrypterBundle]
    git=http://github.com/pierrre/Symfony2-PierrreEncrypterBundle.git
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
	key: "@kernel.secret"
	algorithm: "rijndael-128"
	mode: "cbc"
	useRandomInitializationVector: true
	useBase64: true
	useBase64UrlSafe: true
	enableTwigExtension: true
```

`key` is the secret that is used to encrypt data. By default, it will use the kernel secret.  
`algorithm` and `mode` are the encryption algorithm and mode.  
If you set `useRandomInitializationVector`to false, it will use an blank string as initialization vector.  
Please read the [Mcrypt documentation](http://www.php.net/manual/en/book.mcrypt.php).  

`useBase64` will encode the encrypted data with the base64 algorithm.  
`useBase64UrlSafe` will replace "+" and "/" characters by "-" and "_".  

`enableTwigExtension` controls the Twig extension.

## Usage

Please note:

- If a random initialization vector is used, it will be concatenated at the beginning of the encrypted data
- "\0" characters at the end of the decrypted data will be removed
- "=" characters at the end of the encrypted+base64 data will be removed

### Programmatically

#### With dependency injection

``` php
<?php
$encrypter = $container->get('pierrre_encrypter');
$data = 'foobar';
$encryptedData = $encrypter->encrypt($data);
$decryptedData = $encrypter->decrypt($encryptedData);
```

#### Manually

``` php
<?php
use Pierrre\EncrypterBundle\Util\Encrypter;
$encrypter = new Encrypter('mySecret');
```

### Twig extension

```
{{ data | encrypt }}
```