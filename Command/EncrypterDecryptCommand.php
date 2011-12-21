<?php

namespace PierreDurand\EncrypterBundle\Command;

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
		
		$this->setName('pierredurand:encrypter:decrypt')
		->setDescription('Decrypt data')
		->addArgument('encryptedData', InputArgument::REQUIRED, 'Data to decrypt');
	}
	
	protected function execute(InputInterface $input, OutputInterface $output){
		$encryptedData = $input->getArgument('encryptedData');
		
		$data = $this->getContainer()->get('pierre_durand_encrypter')->decrypt($encryptedData);
		
		$output->writeln($data);
	}
}