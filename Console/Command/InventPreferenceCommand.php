<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;
use Wesleywmd\Invent\Model\ModuleService;
use Wesleywmd\Invent\Model\Preference;
use Wesleywmd\Invent\Model\XmlParser\Location;

class InventPreferenceCommand extends InventCommandBase
{
    protected $successMessage = 'Preference Created Successfully!';
    
    private $preferenceDataFactory;

    public function __construct(
        ComponentInterface $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        Preference\DataFactory $preferenceDataFactory
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->preferenceDataFactory = $preferenceDataFactory;
    }

    protected function configure()
    {
        $this->setName('invent:preference')
            ->setDescription('Create Preference')
            ->addModuleNameArgument()
            ->addArgument('for', InputArgument::REQUIRED, 'Object preference is for')
            ->addArgument('type', InputArgument::REQUIRED, 'Object to use for the preference')
            ->addOption('area', null, InputOption::VALUE_REQUIRED, 'DI load area', Location::AREA_GLOBAL);
    }
    
    protected function getData(InputInterface $input)
    {
        return $this->preferenceDataFactory->create([
            'moduleName' => $moduleName,
            'for' => $input->getArgument('for'),
            'type' => $input->getArgument('type'),
            'area' => $input->getOption('area')
        ]);
    }
}