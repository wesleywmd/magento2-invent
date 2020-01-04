<?php
namespace Wesleywmd\Invent\Console\Command;

use Magento\Setup\Console\Style\MagentoStyleInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

abstract class InventCommandBase extends Command
{
    protected $successMessage = 'Component Created Successfully!';

    protected $component;

    protected $moduleNameFactory;

    protected $inventStyleFactory;

    protected $moduleNameValidator;

    public function __construct(
        ComponentInterface $component,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator
    ) {
        parent::__construct();
        $this->component = $component;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->inventStyleFactory = $inventStyleFactory;
        $this->moduleNameValidator = $moduleNameValidator;
    }

    abstract protected function getData(InputInterface $input);

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        try {
            $this->beforeAddToModule($io, $this->getData($input));
            $this->component->addToModule($this->getData($input));
            $io->success($this->successMessage);
            $this->afterAddToModule($io, $this->getData($input));
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }

    protected function verifyFileNameArgument(MagentoStyleInterface $io, $validator, $question, $argument, $errorMessage)
    {
        do {
            /** @var InventStyle $io */
            $io->askForValidatedArgument($argument, $question, null, $validator, 3);
            $data = $this->getData($io->getInput());
            if (is_file($data->getPath())) {
                $io->error($errorMessage);
                $io->getInput()->setArgument($argument, null);
            }
        } while(is_null($io->getInput()->getArgument($argument)));
    }

    protected function verifyModuleName(MagentoStyleInterface $io, $to = 'component')
    {
        $question = 'What module do you want to add a '.$to.' to?';
        $io->askForValidatedArgument('moduleName', $question, null, $this->moduleNameValidator, 3);
    }

    protected function addModuleNameArgument()
    {
        $this->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name');
        return $this;
    }

    protected function beforeAddToModule(InventStyle $io, DataInterface $data)
    {

    }

    protected function afterAddToModule(InventStyle $io, DataInterface $data)
    {

    }

}