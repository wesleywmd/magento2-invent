<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Controller;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventControllerCommand extends InventCommandBase
{
    protected $successMessage = 'Controller Created Successfully!';

    private $controllerDataFactory;

    private $controllerUrlValidator;

    private $routerValidator;

    public function __construct(
        Controller $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        Controller\DataFactory $controllerDataFactory,
        Controller\ControllerUrlValidator $controllerUrlValidator,
        Controller\RouterValidator $routerValidator
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->controllerDataFactory = $controllerDataFactory;
        $this->controllerUrlValidator = $controllerUrlValidator;
        $this->routerValidator = $routerValidator;
    }

    protected function configure()
    {
        $this->setName('invent:controller')
            ->setDescription('Create Controller')
            ->addModuleNameArgument()
            ->addArgument('controllerUrl', InputArgument::REQUIRED, 'Controller Url')
            ->addOption('router', null, InputOption::VALUE_REQUIRED, 'Router to subscribe the controller to.', 'standard');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        
        $this->verifyModuleName($io, 'controller');
        
        $question = 'What is the controller\'s url?';
        $errorMessage = 'Specified Controller already exists';
        $this->verifyFileNameArgument($io, $this->controllerUrlValidator, $question, 'controllerUrl', $errorMessage);

        $question = 'What router should this controller be associated to?';
        $io->askForValidatedOption('router', $question, 'standard', $this->routerValidator, 3);
    }
    
    protected function getData(InputInterface $input)
    {
        return $this->controllerDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
            'controllerUrl' => $input->getArgument('controllerUrl'),
            'router' => $input->getOption('router')
        ]);
    }
}