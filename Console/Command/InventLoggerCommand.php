<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Logger;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventLoggerCommand extends Command
{
    private $logger;

    private $loggerDataFactory;

    private $moduleNameFactory;

    private $inventStyleFactory;

    private $moduleNameValidator;

    public function __construct(
        Logger $logger,
        Logger\DataFactory $loggerDataFactory,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator
    ) {
        parent::__construct();
        $this->logger = $logger;
        $this->loggerDataFactory = $loggerDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->inventStyleFactory = $inventStyleFactory;
        $this->moduleNameValidator = $moduleNameValidator;
    }

    protected function configure()
    {
        $this->setName('invent:logger')
            ->setDescription('Create Logger')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('loggerName', InputArgument::REQUIRED, 'Logger Name')
            ->addOption('fileName', null, InputOption::VALUE_REQUIRED, 'File Name')
            ->addOption('type', null, InputOption::VALUE_REQUIRED, 'Logger Type', 'INFO');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
//        $io = $this->inventStyleFactory->create(compact('input', 'output'));
//
//        $question = 'What module do you want to add a logger to?';
//        $io->askForValidatedArgument('moduleName', $question, null, $this->moduleNameValidator, 3);
//
//        do {
//            $question = 'What is the console command\'s name?';
//            $io->askForValidatedArgument('commandName', $question, null, $this->blockNameValidator, 3);
//            $commandData = $this->commandDataFactory->create([
//                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
//                'commandName' => $input->getArgument('commandName')
//            ]);
//            if (is_file($commandData->getPath())) {
//                $io->error('Specified Console Command already exists');
//                $input->setArgument('commandName', null);
//            }
//        } while(is_null($input->getArgument('commandName')));
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        try {
            $loggerData = $this->loggerDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'loggerName' => $input->getArgument('loggerName'),
                'fileName' => $input->getOption('fileName'),
                'type' => $input->getOption('type')
            ]);
            $this->logger->addToModule($loggerData);
            $io->success('Logger Created Successfully!');
        } catch(\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }
}