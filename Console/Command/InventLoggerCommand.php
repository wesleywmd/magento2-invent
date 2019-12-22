<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Logger;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventLoggerCommand extends InventCommandBase
{
    protected $successMessage = 'Logger Created Successfully!';

    private $loggerDataFactory;
    
    private $loggerNameValidator;

    public function __construct(
        Logger $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        Logger\DataFactory $loggerDataFactory,
        Logger\LoggerNameValidator $loggerNameValidator
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->loggerDataFactory = $loggerDataFactory;
        $this->loggerNameValidator = $loggerNameValidator;
    }

    protected function configure()
    {
        $this->setName('invent:logger')
            ->setDescription('Create Logger')
            ->addModuleNameArgument()
            ->addArgument('loggerName', InputArgument::REQUIRED, 'Logger Name')
            ->addOption('fileName', null, InputOption::VALUE_REQUIRED, 'File Name')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Logger Type', 'INFO');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));

        $this->verifyModuleName($io, 'logger');

        $question = 'What is the logger\'s name?';
        $errorMessage = 'Specified Logger already exists';
        $this->verifyFileNameArgument($io, $this->loggerNameValidator, $question, 'loggerName', $errorMessage);
    }

    protected function getData(InputInterface $input)
    {
        return $this->loggerDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
            'loggerName' => $input->getArgument('loggerName'),
            'fileName' => $input->getOption('fileName'),
            'type' => $input->getOption('type')
        ]);
    }
}