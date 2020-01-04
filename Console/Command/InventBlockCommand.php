<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Block;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventBlockCommand extends InventCommandBase
{
    protected $successMessage = 'Block Created Successfully!';
    
    private $blockDataFactory;

    private $blockNameValidator;
    
    public function __construct(
        ComponentInterface $component, 
        ModuleNameFactory $moduleNameFactory, 
        InventStyleFactory $inventStyleFactory, 
        ModuleNameValidator $moduleNameValidator,
        Block\DataFactory $blockDataFactory,
        Block\BlockNameValidator $blockNameValidator
    ) {
        parent::__construct($component, $moduleNameFactory, $inventStyleFactory, $moduleNameValidator);
        $this->blockDataFactory = $blockDataFactory;
        $this->blockNameValidator = $blockNameValidator;
    }

    protected function configure()
    {
        $this->setName('invent:block')
            ->setDescription('Create Block')
            ->addModuleNameArgument()
            ->addArgument('blockName', InputArgument::REQUIRED, 'Block Name');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input','output'));

        $this->verifyModuleName($io, 'block');

        $question = 'What is the block\'s name?';
        $errorMessage = 'Specified Block already exists';
        $this->verifyFileNameArgument($io, $this->blockNameValidator, $question, 'blockName', $errorMessage);
    }

    protected function getData(InputInterface $input)
    {
        return $this->blockDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
            'blockName' => $input->getArgument('blockName')
        ]);
    }
}