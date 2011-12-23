<?php

namespace Pierrre\EncrypterBundle\DependencyInjection;

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
		$loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
		$loader->load('services.yml');
		
		$config = $this->processConfiguration($this, $configs);
		$alias = $this->getAlias();
		
		if(!isset($config['key'])){
			$config['key'] = $container->getParameter('kernel.secret');
		}
		
		foreach($config as $key => $value){
			$container->setParameter($alias . '.' . $key, $value);
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
				->scalarNode('algorithm')->defaultValue(MCRYPT_RIJNDAEL_128)->end()
				->scalarNode('mode')->defaultValue(MCRYPT_MODE_CBC)->end()
				->scalarNode('useRandomInitializationVector')->defaultTrue()->end()
				->scalarNode('useBase64')->defaultTrue()->end()
				->scalarNode('useBase64UrlSafe')->defaultTrue()->end()
			->end()
		;
		
		return $treeBuilder;
	}
}