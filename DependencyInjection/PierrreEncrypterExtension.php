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
		
		//Default keys
		$kernelSecret = $container->getParameter('kernel.secret');
		foreach($config['encrypters'] as $name => $encrypter){
			if(!isset($encrypter['key'])){
				$config['encrypters'][$name]['key'] = $kernelSecret;
			}
		}
		
		//Set encrypters parameters
		$container->setParameter($alias . '.encrypters', $config['encrypters']);
		
		//Twig extension
		$twig = $config['twig'];
		if($twig['enabled']){
			$twigDefaultEncrypter = $twig['defaultEncrypter'];
			
			if($twigDefaultEncrypter == null){
				$encrypterNames = array_keys($config['encrypters']);
				$twigDefaultEncrypter = $encrypterNames[0];
			}
			
			$container->setParameter($alias . '.twig.default_encrypter', $twigDefaultEncrypter);
			
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
				->arrayNode('encrypters')
					->requiresAtLeastOneElement()
					->useAttributeAsKey('name')
					->prototype('array')
						->children()
							->scalarNode('key')->end()
							->scalarNode('algorithm')->defaultValue(Encrypter::DEFAULT_ALGORITHM)->end()
							->scalarNode('mode')->defaultValue(Encrypter::DEFAULT_MODE)->end()
							->scalarNode('random_initialization_vector')->defaultValue(Encrypter::DEFAULT_RANDOM_INITIALIZATION_VECTOR)->end()
							->scalarNode('base64')->defaultValue(Encrypter::DEFAULT_BASE64)->end()
							->scalarNode('base64_url_safe')->defaultValue(Encrypter::DEFAULT_BASE64_URL_SAFE)->end()
						->end()
					->end()
				->end()
				->arrayNode('twig')
					->addDefaultsIfNotSet()
					->children()
						->scalarNode('enabled')->defaultFalse()->end()
						->scalarNode('defaultEncrypter')->defaultNull()->end()
					->end()
				->end()
			->end()
		;
		
		return $treeBuilder;
	}
}