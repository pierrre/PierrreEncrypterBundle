<?php

namespace Pierrre\EncrypterBundle\Command;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class EncrypterEncryptCommand extends ContainerAwareCommand{
	/**
	 * @see Symfony\Component\Console\Command.Command::configure()
	 */
	protected function configure(){
		parent::configure();
		
		$this->setName('pierrre:encrypter:encrypt')
		->setDescription('Encrypt data')
		->addArgument('encrypterName', InputArgument::REQUIRED, 'Encrypter name')
		->addArgument('data', InputArgument::REQUIRED, 'Data to encrypt');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$encrypterName = $input->getArgument('encrypterName');
		$data = $input->getArgument('data');
		
		$encrypter = $this->getContainer()->get('pierrre_encrypter_manager')->get($encrypterName);
		$encryptedData = $encrypter->encrypt($data);
		
		$output->writeln($encryptedData);
	}
}