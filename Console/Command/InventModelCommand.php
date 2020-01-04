<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Model;
use Wesleywmd\Invent\Model\Model\Data;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleName;
use Wesleywmd\Invent\Model\ModuleNameFactory;
use Wesleywmd\Invent\Model\Preference;
use Wesleywmd\Invent\Model\XmlParser\Location;

class InventModelCommand extends InventCommandBase
{
    private $modelDataFactory;

    private $modelNameValidator;
    
    private $preference;
    
    private $preferenceDataFactory;
    

    public function __construct(
        ComponentInterface $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        Model\DataFactory $modelDataFactory,
        Model\ModelNameValidator $modelNameValidator,
        ComponentInterface $preference,
        Preference\DataFactory $preferenceDataFactory
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->modelDataFactory = $modelDataFactory;
        $this->modelNameValidator = $modelNameValidator;
        $this->preference = $preference;
        $this->preferenceDataFactory = $preferenceDataFactory;
    }

    protected function configure()
    {
        $this->setName('invent:model')
            ->setDescription('Create Model')
            ->addModuleNameArgument()
            ->addArgument('modelName', InputArgument::REQUIRED, 'Model Name')
            ->addOption('column', null, InputOption::VALUE_REQUIRED|InputOption::VALUE_IS_ARRAY, 'Columns on database table')
            ->addOption('tableName', null, InputOption::VALUE_REQUIRED, 'Database table')
            ->addOption('no-entity-id', null, InputOption::VALUE_NONE, 'Removes standard entity_id column')
            ->addOption('no-created-at', null, InputOption::VALUE_NONE, 'Removes standard created_at column')
            ->addOption('no-updated-at', null, InputOption::VALUE_NONE, 'Removes standard updated_at column');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));

        $this->verifyModuleName($io, 'model');

        $question = 'What is the model\'s name?';
        $errorMessage = 'Specified Model already exists';
        $this->verifyFileNameArgument($io, $this->modelNameValidator, $question, 'modelName', $errorMessage);
    }

    protected function getData(InputInterface $input)
    {
        return $this->modelDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
            'modelName' => $input->getArgument('modelName'),
            'columns' => $input->getOption('column'),
            'tableName' => $input->getOption('tableName'),
            'noEntityId' => $input->getOption('no-entity-id'),
            'noCreatedAt' => $input->getOption('no-created-at'),
            'noUpdatedAt' => $input->getOption('no-updated-at')
        ]);
    }

    protected function afterAddToModule(InventStyle $io, DataInterface $data)
    {
        $this->createPreference($data->getModuleName(), $data->getInterfaceInstance(), $data->getInstance());
        $this->createPreference($data->getModuleName(), $data->getSearchResultsInterfaceInstance(), 'Magento\Framework\Api\SearchResults');
        $this->createPreference($data->getModuleName(), $data->getRepositoryInterfaceInstance(), $data->getRepositoryInstance());
    }

    protected function createPreference(ModuleName $moduleName, $for, $type)
    {
        $preferenceData = $this->preferenceDataFactory->create([
            'moduleName' => $moduleName,
            'for' => $for,
            'type' => $type,
            'area' => Location::AREA_GLOBAL
        ]);
        $this->preference->addToModule($preferenceData);
    }
}