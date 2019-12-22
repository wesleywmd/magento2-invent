<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Cron;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventCronCommand extends InventCommandBase
{
    protected $successMessage = 'Cron Created Successfully!';

    private $cronDataFactory;

    private $cronNameValidator;

    public function __construct(
        Cron $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        Cron\DataFactory $cronDataFactory,
        Cron\CronNameValidator $cronNameValidator
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->cronDataFactory = $cronDataFactory;
        $this->cronNameValidator = $cronNameValidator;
    }

    protected function configure()
    {
        $this->setName('invent:cron')
            ->setDescription('Create Cron Task')
            ->addModuleNameArgument()
            ->addArgument('cronName', InputArgument::REQUIRED, 'Cron Name')
            ->addOption('method', null, InputOption::VALUE_REQUIRED, 'Method Name', 'execute')
            ->addOption('schedule', null, InputOption::VALUE_REQUIRED, 'Cron Schedule', '* * * * *')
            ->addOption('group', null, InputOption::VALUE_REQUIRED, 'Cron Runtime Group', 'default');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));

        $this->verifyModuleName($io, 'cron');

        $question = 'What is the Cron\'s name?';
        $errorMessage = 'Specified Cron already exists';
        $this->verifyFileNameArgument($io, $this->cronNameValidator, $question, 'cronName', $errorMessage);
    }

    protected function getData(InputInterface $input)
    {
        return $this->cronDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
            'cronName' => $input->getArgument('cronName'),
            'method' => $input->getOption('method'),
            'schedule' => $input->getOption('schedule'),
            'group' => $input->getOption('group')
        ]);
    }
}