<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Command;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventCommandCommand extends InventCommandBase
{
    protected $successMessage = 'Console Command Created Successfully!';

    private $commandDataFactory;

    private $commandNameValidator;

    public function __construct(
        ComponentInterface $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        Command\DataFactory $commandDataFactory,
        Command\CommandNameValidator $commandNameValidator
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->commandDataFactory = $commandDataFactory;
        $this->commandNameValidator = $commandNameValidator;
    }

    protected function configure()
    {
        $this->setName('invent:command')
            ->setDescription('Create Console Command')
            ->addModuleNameArgument()
            ->addArgument('commandName', InputArgument::REQUIRED, 'Console Command Name');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));

        $this->verifyModuleName($io, 'console command');

        $question = 'What is the console command\'s name?';
        $errorMessage = 'Specified Console Command already exists';
        $this->verifyFileNameArgument($io, $this->commandNameValidator, $question, 'commandName', $errorMessage);
    }

    protected function getData(InputInterface $input)
    {
        return $this->commandDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
            'commandName' => $input->getArgument('commandName')
        ]);
    }
}