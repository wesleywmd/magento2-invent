<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\Component\CronFactory;


class InventModuleCommand extends Command
{
    private $cronFactory;

    private $moduleForge;

    public function __construct(
        CronFactory $cronFactory,
        \Wesleywmd\Invent\Model\ModuleForge $moduleForge
    ) {
        $this->cronFactory = $cronFactory;
        $this->moduleForge = $moduleForge;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('invent:module')
            ->setDescription('Creates new module')
            ->addArgument("module_name", InputArgument::REQUIRED, "Module Name")
            ->addOption("controller", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Controller Urls to add", [])
            ->addOption("command", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Command name to add", [])
            ->addOption("block", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Blocks to add", [])
            ->addOption("cron", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Cron Tasks to add", [])
            ->addOption("model", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Models to add", []);
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
            $moduleName = $input->getArgument("module_name");
            $this->moduleForge->addModule($moduleName);
            $output->writeln("{$moduleName} Created Successfully!");

            foreach( $input->getOption("controller") as $controller ) {
                $this->moduleForge->addController($moduleName, $controller, "standard");
                $output->writeln("{$controller} Created Successfully!");
            }

            foreach( $input->getOption("command") as $command ) {
                $this->moduleForge->addCommand($moduleName, $command);
                $output->writeln("{$command} Created Successfully!");
            }

            foreach( $input->getOption("block") as $block ) {
                $this->moduleForge->addBlock($moduleName, $block);
                $output->writeln("{$block} Created Successfully!");
            }

            foreach( $input->getOption("cron") as $cronName ) {
                $cron = $this->cronFactory->create(['data'=>[
                    'moduleName'=>$input->getArgument("module_name"),
                    'cronName'=>$cronName,
                    'method'=>'execute',
                    'schedule'=>'* * * * *',
                    'group'=>'default'
                ]]);
                $cron->addToModule();
                $output->writeln("{$cronName} Created Successfully!");
            }

            foreach( $input->getOption("model") as $model ) {
                $this->moduleForge->addModel($moduleName, $model, []);
                $output->writeln("{$model} Created Successfully!");
            }
        } catch( \Exception $e ) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }
}