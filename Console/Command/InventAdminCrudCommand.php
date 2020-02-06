<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\Model;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventAdminCrudCommand extends Command
{
    private $crud;

    private $crudDataFactory;

    private $aclHelper;

    public function __construct(
        Model $model,
        Model\DataFactory $modelDataFactory,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        Crud $crud,
        Crud\DataFactory $crudDataFactory,
        AclHelper $aclHelper
    ) {
        parent::__construct($model, $modelDataFactory, $moduleNameFactory, $inventStyleFactory);
        $this->crud = $crud;
        $this->crudDataFactory = $crudDataFactory;
        $this->aclHelper = $aclHelper;
    }

    protected function configure()
    {
        
        parent::configure();
        $this->setName('invent:admin:crud')
            ->setDescription('Create Admin Crud')
            ->addOption('no-model', null,InputOption::VALUE_NONE, 'Don\'t create matching model')
            ->addOption('menu-title', null, InputOption::VALUE_REQUIRED, 'Menu Title');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        try {
            $modelData = $this->modelDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'modelName' => $input->getArgument('modelName'),
                'columns' => $input->getOption('column'),
                'noEntityId' => $input->getOption('no-entity-id'),
                'noCreatedAt' => $input->getOption('no-created-at'),
                'noUpdatedAt' => $input->getOption('no-updated-at')
            ]);
            if (!$input->getOption('no-model')) {
                $this->model->addToModule($modelData);
            }
            $crudData = $this->crudDataFactory->create([
                'moduleName' => $modelData->getModuleName(),
                'model' => $modelData
            ]);
            $this->crud->addToModule($crudData);
            $io->success('Admin Crud Created Successfully!');
        } catch(\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }
}