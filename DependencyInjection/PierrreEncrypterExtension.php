<?php

namespace Pierrre\EncrypterBundle\DependencyInjection;

use Pierrre\EncrypterBundle\Util\Encrypter;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

class PierrreEncrypterExtension extends Extension implements ConfigurationInterface{
	/**
	 * @see Symfony\Component\DependencyInjection\Extension.ExtensionInterface::load()
	 */
	public function load(array $configs, ContainerBuilder $container){
		//Load config
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('services.yml');
		
		$config = $this->processConfiguration($this, $configs);
		$alias = $this->getAlias();
		
		//Default key
		if(!isset($config['key'])){
			$config['key'] = $container->getParameter('kernel.secret');
		}
		
		//Set parameters (basic)
		foreach($config as $key => $value){
			$container->setParameter($alias . '.' . $key, $value);
		}
		
		//Twig extension
		if($config['enableTwigExtension']){
			$loader->load('twig_extension.yml');
		}
	}
	
	/**
	 * @see Symfony\Component\Config\Definition.ConfigurationInterface::getConfigTreeBuilder()
	 */
	public function getConfigTreeBuilder(){
		$treeBuilder = new TreeBuilder();
		
		$treeBuilder->root($this->getAlias())
			->children()
				->scalarNode('key')->end()
				->scalarNode('algorithm')->defaultValue(Encrypter::DEFAULT_ALGORITHM)->end()
				->scalarNode('mode')->defaultValue(Encrypter::DEFAULT_MODE)->end()
				->scalarNode('useRandomInitializationVector')->defaultValue(Encrypter::DEFAULT_USE_RANDOM_INITIALIZATION_VECTOR)->end()
				->scalarNode('useBase64')->defaultValue(Encrypter::DEFAULT_USE_BASE64)->end()
				->scalarNode('useBase64UrlSafe')->defaultValue(Encrypter::DEFAULT_USE_BASE64_URL_SAFE)->end()
				->scalarNode('enableTwigExtension')->defaultFalse()->end()
			->end()
		;
		
		return $treeBuilder;
	}
}