<?php
namespace Wesleywmd\Invent\Console\Command;

use Magento\Setup\Console\Style\MagentoStyleFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Acl\AclNameValidator;
use Wesleywmd\Invent\Model\Config;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventConfigCommand extends InventCommandAcl
{
    private $configDataFactory;
    
    private $tabIdValidator;
    
    private $tabLabelValidator;

    public function __construct(
        Config $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        AclHelper $aclHelper,
        AclNameValidator $aclNameValidator,
        Config\DataFactory $configDataFactory,
        Config\TabIdValidator $tabIdValidator,
        Config\TabLabelValidator $tabLabelValidator
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator, $aclHelper, $aclNameValidator);
        $this->configDataFactory = $configDataFactory;
        $this->tabIdValidator = $tabIdValidator;
        $this->tabLabelValidator = $tabLabelValidator;
    }

    protected function configure()
    {
        $this->setName('invent:config')
            ->setDescription('Create Store Config')
            ->addModuleNameArgument()
            ->addArgument('configName', InputArgument::REQUIRED, 'Config Name')
            ->addOption('tabId', null, InputOption::VALUE_REQUIRED, 'New Tab Id')
            ->addOption('tabLabel', null, InputOption::VALUE_REQUIRED, 'New Tab Label')
            ->addOption('tabSortOrder', null, InputOption::VALUE_REQUIRED, 'New Tab Sort Order', 10)
            ->addOption('sectionLabel', null, InputOption::VALUE_REQUIRED, 'New Section Label')
            ->addOption('sectionSortOrder', null, InputOption::VALUE_REQUIRED, 'New Section Sort Order', 10)
            ->addOption('sectionShowInDefault', null, InputOption::VALUE_REQUIRED, 'New Section ShowInDefault', 1)
            ->addOption('sectionShowInWebsite', null, InputOption::VALUE_REQUIRED, 'New Section ShowInWebsite', 1)
            ->addOption('sectionShowInStore', null, InputOption::VALUE_REQUIRED, 'New Section ShowInStore', 1)
            ->addOption('sectionClass', null, InputOption::VALUE_REQUIRED, 'New Section Class', 'separator-top')
            ->addOption('sectionTab', null, InputOption::VALUE_REQUIRED, 'New Section Tab')
            ->addOption('sectionResource', null, InputOption::VALUE_REQUIRED, 'New Section Resource')
            ->addOption('groupLabel', null, InputOption::VALUE_REQUIRED, 'New Group Label')
            ->addOption('groupSortOrder', null, InputOption::VALUE_REQUIRED, 'New Group Sort Order', 10)
            ->addOption('groupShowInDefault', null, InputOption::VALUE_REQUIRED, 'New Group ShowInDefault', 1)
            ->addOption('groupShowInWebsite', null, InputOption::VALUE_REQUIRED, 'New Group ShowInWebsite', 1)
            ->addOption('groupShowInStore', null, InputOption::VALUE_REQUIRED, 'New Group ShowInStore', 1)
            ->addOption('fieldLabel', null, InputOption::VALUE_REQUIRED, 'New Field Label')
            ->addOption('fieldType', null, InputOption::VALUE_REQUIRED, 'New Field type', 'text')
            ->addOption('fieldSortOrder', null, InputOption::VALUE_REQUIRED, 'New Field Sort Order', 10)
            ->addOption('fieldShowInDefault', null, InputOption::VALUE_REQUIRED, 'New Field ShowInDefault', 1)
            ->addOption('fieldShowInWebsite', null, InputOption::VALUE_REQUIRED, 'New Field ShowInWebsite', 1)
            ->addOption('fieldShowInStore', null, InputOption::VALUE_REQUIRED, 'New Field ShowInStore', 1)
            ->addOption('fieldComment', null, InputOption::VALUE_REQUIRED, 'New Field Comment');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));

        $this->verifyModuleName($io, 'config');
        
        if (!is_null($input->getOption('tabLabel'))) {
            $question = 'What is the Tab\'s label?';
            $io->askForValidatedOption('tabLabel', $question, null, $this->tabLabelValidator, 3);
            $data = $this->getData($input);

            if (is_null($input->getOption('tabId'))) {
                if (!$io->confirm('Do you want to use the generated Tab Id? "'.$data->getSectionId().'"', false)) {
                    $question = 'What Tab Id do you want to use?';
                    $io->askForValidatedOption('tabId', $question, null, $this->tabIdValidator, 3);
                }
            }
        }

        if (!is_null($input->getOption('sectionLabel'))) {
            $this->verifyAclOption($io, 'sectionResource');
        }
    }

    protected function getData(InputInterface $input)
    {
        return $this->configDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
            'configName' => $input->getArgument('configName'),
            'tabId' => $input->getOption('tabId'),
            'tabLabel' => $input->getOption('tabLabel'),
            'tabSortOrder' => $input->getOption('tabSortOrder'),
            'sectionLabel' => $input->getOption('sectionLabel'),
            'sectionSortOrder' => $input->getOption('sectionSortOrder'),
            'sectionShowInDefault' => $input->getOption('sectionShowInDefault'),
            'sectionShowInWebsite' => $input->getOption('sectionShowInWebsite'),
            'sectionShowInStore' => $input->getOption('sectionShowInStore'),
            'sectionClass' => $input->getOption('sectionClass'),
            'sectionTab' => $input->getOption('sectionTab'),
            'sectionResource' => $input->getOption('sectionResource'),
            'groupLabel' => $input->getOption('groupLabel'),
            'groupSortOrder' => $input->getOption('groupSortOrder'),
            'groupShowInDefault' => $input->getOption('groupShowInDefault'),
            'groupShowInWebsite' => $input->getOption('groupShowInWebsite'),
            'groupShowInStore' => $input->getOption('groupShowInStore'),
            'fieldLabel' => $input->getOption('fieldLabel'),
            'fieldType' => $input->getOption('fieldType'),
            'fieldSortOrder' => $input->getOption('fieldSortOrder'),
            'fieldShowInDefault' => $input->getOption('fieldShowInDefault'),
            'fieldShowInWebsite' => $input->getOption('fieldShowInWebsite'),
            'fieldShowInStore' => $input->getOption('fieldShowInStore'),
            'fieldComment' => $input->getOption('fieldComment')
        ]);
    }
}