<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\ModuleFactory;
use Wesleywmd\Invent\Model\ModuleNameFactory;

abstract class InventBaseCommand extends Command
{
    protected $moduleNameFactory;

    protected $moduleFactory;

    protected $module;

    public function __construct(
        ModuleNameFactory $moduleNameFactory,
        ModuleFactory $moduleFactory
    ) {
        parent::__construct();
        $this->moduleNameFactory = $moduleNameFactory;
        $this->moduleFactory = $moduleFactory;
    }

    protected function configure()
    {
        $this->addArgument('module_name', InputArgument::REQUIRED, 'Module Name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $moduleName = $this->moduleNameFactory->create($input->getArgument('module_name'));
        $this->module = $this->moduleFactory->create($moduleName);
        if( !$this->module->exists() ) {
            throw new \Exception('Module does not exist');
        }
    }

}