<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Model\Controller;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleFactory;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventControllerCommand extends Command
{
    private $controller;

    private $controllerDataFactory;

    private $moduleNameFactory;

    private $moduleNameValidator;

    private $controllerUrlValidator;

    private $routerValidator;

    public function __construct(
        Controller $controller,
        Controller\DataFactory $controllerDataFactory,
        ModuleNameFactory $moduleNameFactory,
        ModuleNameValidator $moduleNameValidator,
        Controller\ControllerUrlValidator $controllerUrlValidator,
        Controller\RouterValidator $routerValidator
    ) {
        parent::__construct();
        $this->controller = $controller;
        $this->controllerDataFactory = $controllerDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->moduleNameValidator = $moduleNameValidator;
        $this->controllerUrlValidator = $controllerUrlValidator;
        $this->routerValidator = $routerValidator;
    }

    protected function configure()
    {
        $this->setName('invent:controller')
            ->setDescription('Create Controller')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('controllerUrl', InputArgument::REQUIRED, 'Controller Url')
            ->addOption('router', null, InputOption::VALUE_REQUIRED, 'Router to subscribe the controller to.', 'standard');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        /** @var InventStyle $io */
        $io = $this->inventStyleFactory->create(compact('input', 'output'));

        $question = 'What module do you want to add a controller to?';
        $io->askForValidatedArgument('moduleName', $question, null, $this->moduleNameValidator, 3);

        do {
            $question = 'What is the controller\'s url?';
            $io->askForValidatedArgument('controllerUrl', $question, null, $this->controllerUrlValidator, 3);
            $controllerData = $this->controllerDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'controllerUrl' => $input->getArgument('controllerUrl'),
                'router' => $input->getOption('router')
            ]);
            if (is_file($controllerData->getPath())) {
                $io->error('Specified Controller already exists');
                $input->setArgument('controllerUrl', null);
            }
        } while(is_null($input->getArgument('controllerUrl')));

        $question = 'What router should this controller be associated to?';
        $io->askForValidatedOption('router', $question, 'standard', $this->routerValidator, 3);
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        /** @var InventStyle $io */
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        try {
            $controllerData = $this->controllerDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'controllerUrl' => $input->getArgument('controllerUrl'),
                'router' => $input->getOption('router')
            ]);
            $this->controller->addToModule($controllerData);
            $io->success('Controller Created Successfully!');
        } catch(\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }
}