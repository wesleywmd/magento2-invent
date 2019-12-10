<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\ModuleNameFactory;
use Wesleywmd\Invent\Model\ModuleService;
use Wesleywmd\Invent\Model\Preference;
use Wesleywmd\Invent\Model\XmlParser\Location;

class InventPreferenceCommand extends Command
{
    private $preference;

    private $preferenceDataFactory;

    private $moduleNameFactory;

    public function __construct(
        Preference $preference,
        Preference\DataFactory $preferenceDataFactory,
        ModuleNameFactory $moduleNameFactory
    ) {
        parent::__construct();
        $this->preference = $preference;
        $this->preferenceDataFactory = $preferenceDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('invent:preference')
            ->setDescription('Create Preference')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('for', InputArgument::REQUIRED, 'Object preference is for')
            ->addArgument('type', InputArgument::REQUIRED, 'Object to use for the preference')
            ->addOption('area', null, InputOption::VALUE_REQUIRED, 'DI load area', Location::AREA_GLOBAL);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleName = $this->moduleNameFactory->create($input->getArgument('moduleName'));
            $preferenceData = $this->preferenceDataFactory->create([
                'moduleName' => $moduleName,
                'for' => $input->getArgument('for'),
                'type' => $input->getArgument('type'),
                'area' => $input->getOption('area')
            ]);
            $this->preference->addToModule($preferenceData);
            $output->writeln('Preference Created Successfully!');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }
}