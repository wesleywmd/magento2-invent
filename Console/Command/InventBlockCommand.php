<?php
namespace Wesleywmd\Invent\Console\Command;

use Magento\Setup\Console\InputValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Block;
use Wesleywmd\Invent\Model\Module\ModuleNameValidator;
use Wesleywmd\Invent\Model\ModuleNameException;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventBlockCommand extends Command
{
    private $block;

    private $blockDataFactory;

    private $moduleNameFactory;

    private $inventStyleFactory;

    private $moduleNameValidator;

    private $blockNameValidator;

    public function __construct(
        Block $block,
        Block\DataFactory $blockDataFactory,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory,
        ModuleNameValidator $moduleNameValidator,
        Block\BlockNameValidator $blockNameValidator
    ) {
        parent::__construct();
        $this->block = $block;
        $this->blockDataFactory = $blockDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->inventStyleFactory = $inventStyleFactory;
        $this->moduleNameValidator = $moduleNameValidator;
        $this->blockNameValidator = $blockNameValidator;
    }

    protected function configure()
    {
        $this->setName('invent:block')
            ->setDescription('Create Block')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('blockName', InputArgument::REQUIRED, 'Block Name');
    }

    protected function interact(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input','output'));

        $question = 'What module do you want to add a block to?';
        $io->askForValidatedArgument('moduleName', $question, null, $this->moduleNameValidator, 3);

        do {
            $question = 'What is the block\'s name?';
            $io->askForValidatedArgument('blockName', $question, null, $this->blockNameValidator, 3);
            $blockData = $this->blockDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'blockName' => $input->getArgument('blockName')
            ]);
            if (is_file($blockData->getPath())) {
                $io->error('Specified Block already exists');
                $input->setArgument('blockName', null);
            }
        } while(is_null($input->getArgument('blockName')));
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = $this->inventStyleFactory->create(compact('input', 'output'));
        try {
            $blockData = $this->blockDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'blockName' => $input->getArgument('blockName')
            ]);
            $this->block->addToModule($blockData);
            $io->success('Block Created Successfully!');
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }
}