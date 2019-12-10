<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\Component\CronFactory;
use Wesleywmd\Invent\Model\Cron;
use Wesleywmd\Invent\Model\ModuleNameFactory;
use Wesleywmd\Invent\Model\ModuleFactory;

class InventCronCommand extends Command
{
    private $cron;

    private $cronDataFactory;

    private $moduleNameFactory;

    public function __construct(
        Cron $cron,
        Cron\DataFactory $cronDataFactory,
        ModuleNameFactory $moduleNameFactory
    ) {
        parent::__construct();
        $this->cron = $cron;
        $this->cronDataFactory = $cronDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
    }

    protected function configure()
    {
        parent::configure();
        $this->setName('invent:cron')
            ->setDescription('Create Cron Task')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('cronName', InputArgument::REQUIRED, 'Cron Name')
            ->addOption('method', null, InputOption::VALUE_REQUIRED, 'Method Name', 'execute')
            ->addOption('schedule', null, InputOption::VALUE_REQUIRED, 'Cron Schedule', '* * * * *')
            ->addOption('group', null, InputOption::VALUE_REQUIRED, 'Cron Runtime Group', 'default');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $cronData = $this->cronDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'cronName' => $input->getArgument('cronName'),
                'method' => $input->getOption('method'),
                'schedule' => $input->getOption('schedule'),
                'group' => $input->getOption('group')
            ]);
            $this->cron->addToModule($cronData);
            $output->writeln('Cron Created Successfully!');
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}