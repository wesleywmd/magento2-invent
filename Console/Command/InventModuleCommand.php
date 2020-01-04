<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Block;
use Wesleywmd\Invent\Model\Command;
use Wesleywmd\Invent\Model\Controller;
use Wesleywmd\Invent\Model\Cron;
use Wesleywmd\Invent\Model\Model;
use Wesleywmd\Invent\Model\Module;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventModuleCommand extends InventCommandBase
{
    private $block;

    private $blockDataFactory;

    private $command;

    private $commandDataFactory;

    private $controller;

    private $controllerDataFactory;

    private $cron;

    private $cronDataFactory;

    private $model;

    private $modelDataFactory;

    private $module;

    private $moduleDataFactory;

    public function __construct(
        ComponentInterface $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        ComponentInterface $block,
        Block\DataFactory $blockDataFactory,
        ComponentInterface $command,
        Command\DataFactory $commandDataFactory,
        ComponentInterface $controller,
        Controller\DataFactory $controllerDataFactory,
        ComponentInterface $cron,
        Cron\DataFactory $cronDataFactory,
        ComponentInterface $model,
        Model\DataFactory $modelDataFactory,
        Module\DataFactory $moduleDataFactory
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->block = $block;
        $this->blockDataFactory = $blockDataFactory;
        $this->command = $command;
        $this->commandDataFactory = $commandDataFactory;
        $this->controller = $controller;
        $this->controllerDataFactory = $controllerDataFactory;
        $this->cron = $cron;
        $this->cronDataFactory = $cronDataFactory;
        $this->model = $model;
        $this->modelDataFactory = $modelDataFactory;
        $this->moduleDataFactory = $moduleDataFactory;
    }

    protected function configure()
    {
        $this->setName('invent:module')
            ->setDescription('Creates new module')
            ->addModuleNameArgument()
            ->addOption('block', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Blocks to add', [])
            ->addOption('command', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Command name to add', [])
            ->addOption('controller', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Controller Urls to add', [])
            ->addOption('cron', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Cron Tasks to add', [])
            ->addOption('model', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Models to add', []);
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {

    }

    protected function getData(InputInterface $input)
    {
        return $this->moduleDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName'))
        ]);
    }

    protected function afterAddToModule(InventStyle $io, DataInterface $data)
    {
        $this->addComponents($io, 'block', 'blockName', $this->block, $this->blockDataFactory);
        $this->addComponents($io, 'command', 'commandName', $this->command, $this->commandDataFactory);
        $this->addComponents($io, 'controller', 'controllerUrl', $this->controller, $this->controllerDataFactory);
        $this->addComponents($io, 'cron', 'cronName', $this->cron, $this->cronDataFactory);
        $this->addComponents($io, 'model', 'modelName', $this->model, $this->modelDataFactory);
    }

    private function addComponents(InventStyle $io, $option, $nameKey, ComponentInterface $component, $dataFactory)
    {
        foreach( $io->getInput()->getOption($option) as $name ) {
            $data = $dataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($io->getInput()->getArgument('moduleName')),
                $nameKey => $name
            ]);
            $component->addToModule($data);
            $io->success($name.' Created Successfully!');
        }
    }
}