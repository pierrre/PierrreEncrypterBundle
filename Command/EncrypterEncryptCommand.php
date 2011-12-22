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
		->addArgument('data', InputArgument::REQUIRED, 'Data to encrypt');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$data = $input->getArgument('data');
		
		$encryptedData = $this->getContainer()->get('pierrre_encrypter')->encrypt($data);
		
		$output->writeln($encryptedData);
	}
}