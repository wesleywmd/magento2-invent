<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Command;
use Wesleywmd\Invent\Model\Component\CommandFactory;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleFactory;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventCommandCommand extends ConsoleCommand
{
    private $command;

    private $commandDataFactory;

    private $moduleNameFactory;

    private $inventStyleFactory;
    
    private $moduleNameValidator;
    
    private $commandNameValidator;

    public function __construct(
        Command $command,
        Command\DataFactory $commandDataFactory,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        Command\CommandNameValidator $commandNameValidator
    ) {
        parent::__construct();
        $this->command = $command;
        $this->commandDataFactory = $commandDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->inventStyleFactory = $inventStyleFactory;
        $this->moduleNameValidator = $moduleNameValidator;
        $this->commandNameValidator = $commandNameValidator;
    }

    protected function configure()
    {
        $this->setName('invent:command')
            ->setDescription('Create Console Command')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('commandName', InputArgument::REQUIRED, 'Console Command Name');
    }
    
    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        
        $question = 'What module do you want to add a console command to?';
        $io->askForValidatedArgument('moduleName', $question, null, $this->moduleNameValidator, 3);

        do {
            $question = 'What is the console command\'s name?';
            $io->askForValidatedArgument('commandName', $question, null, $this->blockNameValidator, 3);
            $commandData = $this->commandDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'commandName' => $input->getArgument('commandName')
            ]);
            if (is_file($commandData->getPath())) {
                $io->error('Specified Console Command already exists');
                $input->setArgument('commandName', null);
            }
        } while(is_null($input->getArgument('commandName')));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        try {
            $commandData = $this->commandDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'commandName' => $input->getArgument('commandName')
            ]);
            $this->command->addToModule($commandData);
            $io->success('Console Command Created Successfully!');
        } catch(\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }
}