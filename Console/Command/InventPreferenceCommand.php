<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InventPreferenceCommand extends \Symfony\Component\Console\Command\Command
{
    const ARGUMENT_BLOCK_NAME = "block_name";

    private $moduleForge;
    
    public function __construct(\Wesleywmd\Invent\Model\ModuleForge $moduleForge) {
        $this->moduleForge = $moduleForge;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName("invent:preference")
            ->setDescription("Create Preference")
            ->addArgument("module_name", InputArgument::REQUIRED, "Module Name")
            ->addArgument("for", InputArgument::REQUIRED, "Object preference is for")
            ->addArgument("type", InputArgument::REQUIRED, "Object to use for the preference");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument("module_name");
        $for = $input->getArgument("for");
        $type = $input->getArgument("type");
        try {
            $this->moduleForge->addPreference($moduleName, $for, $type);
            $output->writeln("Perference Created Successfully!");
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}