<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\Block;
use Wesleywmd\Invent\Model\Command;
use Wesleywmd\Invent\Model\Controller;
use Wesleywmd\Invent\Model\Cron;
use Wesleywmd\Invent\Model\Model;
use Wesleywmd\Invent\Model\Module;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventModuleCommand extends ConsoleCommand
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

    private $moduleNameFactory;

    public function __construct(
        Block $block,
        Block\DataFactory $blockDataFactory,
        Command $command,
        Command\DataFactory $commandDataFactory,
        Controller $controller,
        Controller\DataFactory $controllerDataFactory,
        Cron $cron,
        Cron\DataFactory $cronDataFactory,
        Model $model,
        Model\DataFactory $modelDataFactory,
        Module $module,
        Module\DataFactory $moduleDataFactory,
        ModuleNameFactory $moduleNameFactory
    ) {
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
        $this->module = $module;
        $this->moduleDataFactory = $moduleDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('invent:module')
            ->setDescription('Creates new module')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addOption('controller', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Controller Urls to add', [])
            ->addOption('command', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Command name to add', [])
            ->addOption('block', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Blocks to add', [])
            ->addOption('cron', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Cron Tasks to add', [])
            ->addOption('model', null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, 'Models to add', []);
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int
     * @throws \Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleName = $this->moduleNameFactory->create($input->getArgument('moduleName'));
            $moduleData = $this->moduleDataFactory->create(compact('moduleName'));
            $this->module->addToModule($moduleData);
            $output->writeln($input->getArgument('moduleName').' Created Successfully!');

            foreach( $input->getOption('block') as $blockName ) {
                $blockData = $this->blockDataFactory->create(compact('moduleName', 'blockName'));
                $this->block->addToModule($blockData);
                $output->writeln($blockName.' Created Successfully!');
            }

            foreach( $input->getOption('command') as $commandName ) {
                $commandData = $this->commandDataFactory->create(compact('moduleName', 'commandName'));
                $this->command->addToModule($commandData);
                $output->writeln($commandName.' Created Successfully!');
            }

            $router = 'standard';
            foreach( $input->getOption('controller') as $controllerUrl ) {
                $controllerData = $this->controllerDataFactory->create(compact('moduleName', 'controllerUrl', 'router'));
                $this->controller->addToModule($controllerData);
                $output->writeln($controllerUrl.' Created Successfully!');
            }

            $method = 'execute';
            $schedule = '* * * * *';
            $group = 'default';
            foreach( $input->getOption('cron') as $cronName ) {
                $cronData = $this->cronDataFactory->create(compact('moduleName', 'cronName', 'method', 'schedule', 'group'));
                $this->cron->addToModule($cronData);
                $output->writeln($cronName.' Created Successfully!');
            }

            $columns = [];
            $noEntityId = false;
            $noCreatedAt = false;
            $noUpdatedAt = false;
            foreach( $input->getOption('model') as $modelName ) {
                $modelData = $this->modelDataFactory->create(compact('moduleName', 'modelName', 'columns', 'noEntityId', 'noCreatedAt', 'noUpdatedAt'));
                $this->model->addToModule($modelData);
                $output->writeln($modelName.' Created Successfully!');
            }
        } catch( \Exception $e ) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }
}