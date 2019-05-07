<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InventModelCommand extends Command
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
        $this->setName('invent:model')
            ->setDescription('Create Model')
            ->addArgument("module_name", InputArgument::REQUIRED, "Module Name")
            ->addArgument("model_name", InputArgument::REQUIRED, "Model Name")
            ->addOption("column", null, InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, "Columns on database table.");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleName = $input->getArgument("module_name");
            $modelName = $input->getArgument("model_name");
            $columns = $input->getOption("column");
            $this->moduleForge->addModel($moduleName, $modelName, $columns);
            $output->writeln("{$modelName} Created Successfully!");
            return 0;
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}