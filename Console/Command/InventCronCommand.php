<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InventCronCommand extends Command
{
    private $moduleForge;

    public function __construct(
        \Wesleywmd\Invent\Model\ModuleForge $moduleForge
    ) {
        $this->moduleForge = $moduleForge;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('invent:cron')
            ->setDescription('Create Cron Task')
            ->addArgument("module_name", InputArgument::REQUIRED, "Module Name")
            ->addArgument("cron_name", InputArgument::REQUIRED, "Cron Name")
            ->addOption("method", null, InputOption::VALUE_REQUIRED, "Mathod Name", "execute")
            ->addOption("schedule", null, InputOption::VALUE_REQUIRED, "Cron Schedule", "* * * * *")
            ->addOption("group", null, InputOption::VALUE_REQUIRED, "Cron Runtime Group", "default");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleName = $input->getArgument("module_name");
            $cronName = $input->getArgument("cron_name");
            $method = $input->getOption("method");
            $schedule = $input->getOption("schedule");
            $group = $input->getOption("group");
            $this->moduleForge->addCron($moduleName, $cronName, $method, $schedule, $group);
            $output->writeln("{$cronName} Created Successfully!");
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}