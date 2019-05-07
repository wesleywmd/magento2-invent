<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InventModuleCommand extends Command
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

            foreach( $input->getOption("cron") as $cron ) {
                $this->moduleForge->addCron($moduleName, $cron, "execute", "* * * * *", "default");
                $output->writeln("{$cron} Created Successfully!");
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