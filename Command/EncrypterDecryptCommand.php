<?php

namespace Pierrre\EncrypterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncrypterDecryptCommand extends ContainerAwareCommand{
	/**
	 * @see Symfony\Component\Console\Command.Command::configure()
	 */
	protected function configure(){
		parent::configure();
		
		$this->setName('pierrre:encrypter:decrypt')
		->setDescription('Decrypt data')
		->addArgument('encrypterName', InputArgument::REQUIRED, 'Encrypter name')
		->addArgument('encryptedData', InputArgument::REQUIRED, 'Data to decrypt');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$encrypterName = $input->getArgument('encrypterName');
		$encryptedData = $input->getArgument('encryptedData');
		
		$encrypter = $this->getContainer()->get('pierrre_encrypter.manager')->get($encrypterName);
		$data = $encrypter->decrypt($encryptedData);
		
		$output->writeln($data);
	}
}