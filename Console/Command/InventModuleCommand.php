<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Exception\ModuleServiceException;

class InventModuleCommand extends Command
{
    private $moduleService;
    private $registrationRenderer;
    private $moduleXmlService;
    private $addCommandService;
    private $addControllerService;
    private $addBlockService;
    private $addCronService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Php\RegistrationRenderer $registrationRenderer,
        \Wesleywmd\Invent\Service\Xml\ModuleXmlService $moduleXmlService,
        \Wesleywmd\Invent\Service\AddControllerService $addControllerService,
        \Wesleywmd\Invent\Service\AddCommandService $addCommandService,
        \Wesleywmd\Invent\Service\AddBlockService $addBlockService,
        \Wesleywmd\Invent\Service\AddCronService $addCronService
    ) {
        $this->moduleService = $moduleService;
        $this->registrationRenderer = $registrationRenderer;
        $this->moduleXmlService = $moduleXmlService;
        $this->addCommandService = $addCommandService;
        $this->addControllerService = $addControllerService;
        $this->addBlockService = $addBlockService;
        $this->addCronService = $addCronService;
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
            ->addOption("cron", null, InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY, "Cron Tasks to add", []);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleName = $input->getArgument("module_name");
            if( $this->moduleService->isDirectory($moduleName) ) {
                throw new ModuleServiceException("Cannot Create Module, directory already exists.");
            }
            $registrationString = $this->registrationRenderer->render($moduleName);
            $this->moduleService->makeFile("registration.php", $registrationString, $moduleName);
            $this->moduleXmlService->registerModule($moduleName, "0.0.1");
            $output->writeln("{$moduleName} Created Successfully!");

            foreach( $input->getOption("controller") as $controller ) {
                $this->addControllerService->execute($moduleName, $controller, "standard");
                $output->writeln("{$controller} Created Successfully!");
            }

            foreach( $input->getOption("command") as $command ) {
                $this->addCommandService->execute($moduleName, $command);
                $output->writeln("{$command} Created Successfully!");
            }

            foreach( $input->getOption("block") as $block ) {
                $this->addBlockService->execute($moduleName, $block);
                $output->writeln("{$block} Created Successfully!");
            }

            foreach( $input->getOption("cron") as $cron ) {
                $this->addCronService->execute($moduleName, $cron, "execute", "* * * * *", "default");
                $output->writeln("{$cron} Created Successfully!");
            }
        } catch( ModuleServiceException $e ) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}