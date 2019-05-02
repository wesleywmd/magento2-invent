<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InventCommandCommand extends Command
{
    private $moduleForge;

    public function __construct(
        \Wesleywmd\Invent\Model\ModuleForge $moduleForge
    ) {
        $this->moduleForge = $moduleForge;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('invent:command')
            ->setDescription('Create Console Command')
            ->addArgument("module_name", InputArgument::REQUIRED, "Module Name")
            ->addArgument("command_name", InputArgument::REQUIRED, "Console Command Name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleName = $input->getArgument("module_name");
            $commandName = $input->getArgument("command_name");
            $this->moduleForge->addCommand($moduleName, $commandName);
            $output->writeln("{$commandName} Created Successfully!");
            return 0;
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}