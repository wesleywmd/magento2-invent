<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InventBlockCommand extends \Symfony\Component\Console\Command\Command
{
    const ARGUMENT_MODULE_NAME = "module_name";
    const ARGUMENT_BLOCK_NAME = "block_name";

    private $moduleForge;
    
    public function __construct(
        \Wesleywmd\Invent\Model\ModuleForge $moduleForge
    ) {
        $this->moduleForge = $moduleForge;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName("invent:block")
            ->setDescription("Create Block")
            ->addArgument(self::ARGUMENT_MODULE_NAME, InputArgument::REQUIRED, "Module Name")
            ->addArgument(self::ARGUMENT_BLOCK_NAME, InputArgument::REQUIRED, "Block Name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $input->getArgument(self::ARGUMENT_MODULE_NAME);
        $blockName = $input->getArgument(self::ARGUMENT_BLOCK_NAME);
        try {
            $this->moduleForge->addBlock($moduleName, $blockName);
            $output->writeln(sprintf("%s Created Successfully!", $blockName));
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}