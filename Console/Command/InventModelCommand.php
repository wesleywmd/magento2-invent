<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\Model;
use Wesleywmd\Invent\Model\Model\DataFactory;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventModelCommand extends Command
{
    private $model;

    private $modelDataFactory;

    private $moduleNameFactory;

    public function __construct(
        Model $model,
        Model\DataFactory $modelDataFactory,
        ModuleNameFactory $moduleNameFactory
    ) {
        parent::__construct();
        $this->model = $model;
        $this->modelDataFactory = $modelDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
    }

    protected function configure()
    {
        $this->setName('invent:model')
            ->setDescription('Create Model')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('modelName', InputArgument::REQUIRED, 'Model Name')
            ->addOption('column', null, InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Columns on database table')
            ->addOption('no-entity-id', null, InputOption::VALUE_NONE, 'removes standard entity_id column')
            ->addOption('no-created-at', null, InputOption::VALUE_NONE, 'removes standard created_at column')
            ->addOption('no-updated-at', null, InputOption::VALUE_NONE, 'removes standard updated_at column');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $modelData = $this->modelDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'modelName' => $input->getArgument('modelName'),
                'columns' => $input->getOption('column'),
                'noEntityId' => $input->getOption('no-entity-id'),
                'noCreatedAt' => $input->getOption('no-created-at'),
                'noUpdatedAt' => $input->getOption('no-updated-at')
            ]);
            $this->model->addToModule($modelData);
            $output->writeln('Model Created Successfully!');
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}