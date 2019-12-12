<?php
namespace Wesleywmd\Invent\Console\Command;

use Magento\Setup\Console\Style\MagentoStyleFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Config;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventConfigCommand extends Command
{
    private $config;

    private $configDataFactory;

    private $moduleNameFactory;

    private $magentoStyleFactory;

    private $aclHelper;

    public function __construct(
        Config $config,
        Config\DataFactory $configDataFactory,
        ModuleNameFactory $moduleNameFactory,
        MagentoStyleFactory $magentoStyleFactory,
        AclHelper $aclHelper
    ) {
        parent::__construct();
        $this->config = $config;
        $this->configDataFactory = $configDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->magentoStyleFactory = $magentoStyleFactory;
        $this->aclHelper = $aclHelper;
    }

    protected function configure()
    {
        $this->setName('invent:config')
            ->setDescription('Create Store Config')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('configName', InputArgument::REQUIRED, 'Config Name')
            ->addOption('createSection', null, InputOption::VALUE_NONE, 'Create New Section Definition')
            ->addOption('createGroup', null, InputOption::VALUE_NONE, 'Create New Group Definition')
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

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $configData = $this->configDataFactory->create([
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
            $this->config->addToModule($configData);
            $output->writeln('Config Created Successfully!');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }
}