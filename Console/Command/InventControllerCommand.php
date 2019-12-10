<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\Controller;
use Wesleywmd\Invent\Model\ModuleFactory;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventControllerCommand extends Command
{
    private $controller;

    private $controllerDataFactory;

    private $moduleNameFactory;

    public function __construct(
        Controller $controller,
        Controller\DataFactory $controllerDataFactory,
        ModuleNameFactory $moduleNameFactory
    ) {
        parent::__construct();
        $this->controller = $controller;
        $this->controllerDataFactory = $controllerDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
    }

    protected function configure()
    {
        $this->setName('invent:controller')
            ->setDescription('Create Controller')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('controllerUrl', InputArgument::REQUIRED, 'Controller Url')
            ->addOption('router', null, InputOption::VALUE_REQUIRED, 'Router to subscribe the controller to.', 'standard');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $controllerData = $this->controllerDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'controllerUrl' => $input->getArgument('controllerUrl'),
                'router' => $input->getOption('router')
            ]);
            $this->controller->addToModule($controllerData);
            $output->writeln('Controller Created Successfully!');
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }
}