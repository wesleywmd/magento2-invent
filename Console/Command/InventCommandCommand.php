<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command as ConsoleCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\Command;
use Wesleywmd\Invent\Model\Component\CommandFactory;
use Wesleywmd\Invent\Model\ModuleFactory;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventCommandCommand extends ConsoleCommand
{
    private $command;

    private $commandDataFactory;

    private $moduleNameFactory;

    public function __construct(
        Command $command,
        Command\DataFactory $commandDataFactory,
        ModuleNameFactory $moduleNameFactory
    ) {
        parent::__construct();
        $this->command = $command;
        $this->commandDataFactory = $commandDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
    }

    protected function configure()
    {
        $this->setName('invent:command')
            ->setDescription('Create Console Command')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('commandName', InputArgument::REQUIRED, 'Console Command Name');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $commandData = $this->commandDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'commandName' => $input->getArgument('commandName')
            ]);
            $this->command->addToModule($commandData);
            $output->writeln('Console Command Created Successfully!');
        } catch(\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}