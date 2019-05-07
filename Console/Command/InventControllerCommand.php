<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InventControllerCommand extends Command
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
        $this->setName('invent:controller')
            ->setDescription('Create Controller')
            ->addArgument("module_name", InputArgument::REQUIRED, "Module Name")
            ->addArgument("controller_url", InputArgument::REQUIRED, "Controller Url")
            ->addOption("router", null, InputOption::VALUE_REQUIRED, "Router to subscribe the controller to.", "standard");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleName = $input->getArgument("module_name");
            $controllerUrl = $input->getArgument("controller_url");
            $router = $input->getOption("router");
            $this->moduleForge->addController($moduleName, $controllerUrl, $router);
            $output->writeln("{$controllerUrl} Created Successfully!");
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}