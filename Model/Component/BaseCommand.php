<?php
namespace Wesleywmd\Invent\Model\Component;

use Magento\Setup\Console\Style\MagentoStyleInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\InterceptorInterface;
use Wesleywmd\Invent\Api\ValidatorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;

class BaseCommand extends Command
{
    protected $inventStyleFactory;

    protected $commandDefinition;

    protected $component;

    protected $dataFactory;

    protected $validator;

    protected $interceptor;

    protected $successMessage;

    protected $commandDescription;

    protected $commandHelp;

    protected $arguments;

    public function __construct(
        InventStyleFactory $inventStyleFactory,
        BaseCommandDefinition $commandDefinition,
        ComponentInterface $component,
        DataFactoryInterface $dataFactory,
        BaseValidator $validator,
        BaseInterceptor $interceptor
    ) {
        $this->inventStyleFactory = $inventStyleFactory;
        $this->commandDefinition = $commandDefinition;
        $this->component = $component;
        $this->dataFactory = $dataFactory;
        $this->validator = $validator;
        $this->interceptor = $interceptor;
        parent::__construct($this->commandDefinition->getName());
    }

    protected function configure()
    {
        $this->setDescription($this->commandDefinition->getDescription())
            ->setHelp($this->commandDefinition->getHelp())
            ->addModuleNameArgument();

        foreach ($this->commandDefinition->getArguments() as $name=>$argument) {
            $this->addArgument(
                $name,
                $argument['mode'] ?? null,
                $argument['description'] ?? '',
                $argument['default'] ?? null
            );
        }

        foreach ($this->commandDefinition->getOptions() as $name=>$option) {
            $this->addOption(
                $name,
                $option['shortcut'] ?? null,
                $option['mode'] ?? null,
                $option['description'] ?? '',
                $option['default'] ?? null
            );
        }
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input','output'));
        if (!is_null($this->validator)) {
            $this->validator->validate($io);
        }
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        $data = $this->dataFactory->create($input);
        try {
            if (!is_null($this->interceptor)) {
                $data = $this->interceptor->before($io, $data);
            }
            $this->component->addToModule($data);
            $io->success($this->commandDefinition->getSuccessMessage());
            if (!is_null($this->interceptor)) {
                $this->interceptor->after($io, $data);
            }
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }

    protected function addModuleNameArgument()
    {
        $this->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name');
        return $this;
    }
}