<?php
namespace Wesleywmd\Invent\Console\Command;

use Magento\Setup\Console\InputValidationException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Console\InventStyleFactory;
use Wesleywmd\Invent\Model\Block;
use Wesleywmd\Invent\Model\ModuleNameException;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventBlockCommand extends Command
{
    private $block;

    private $blockDataFactory;

    private $moduleNameFactory;

    private $inventStyleFactory;

    public function __construct(
        Block $block,
        Block\DataFactory $blockDataFactory,
        ModuleNameFactory $moduleNameFactory,
        InventStyleFactory $inventStyleFactory
    ) {
        parent::__construct();
        $this->block = $block;
        $this->blockDataFactory = $blockDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
        $this->inventStyleFactory = $inventStyleFactory;
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
        $io->askForValidatedArgument('moduleName', $question, null, function($moduleName) {
            if (is_null($moduleName)) {
                throw new InputValidationException('moduleName is required');
            }
            try {
                $name = $this->moduleNameFactory->create($moduleName);
            } catch (ModuleNameException $e) {
                throw new InputValidationException($e->getMessage());
            }
            if (!is_dir($name->getPath())) {
                throw new InputValidationException('Specified Module does not exist');
            }
            return $moduleName;
        }, 3);

        $question = 'What is the block\'s name?';
        $io->askForValidatedArgument('blockName', $question, null, function($blockName) use ($input) {
            if (is_null($blockName)) {
                throw new InputValidationException('blockName is required');
            }
            try {
                $blockData = $this->getBlockData($input->getArgument('moduleName'), $blockName);
                if (is_file($blockData->getPath())) {
                    throw new InputValidationException('Specified Block already exists');
                }
            } catch (ModuleNameException $e) {
                throw new InputValidationException($e->getMessage());
            }
            return $blockName;
        }, 3);
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
            $blockData = $this->getBlockData(
                $input->getArgument('moduleName'),
                $input->getArgument('blockName')
            );
            $this->block->addToModule($blockData);
            $io->success('Block Created Successfully!');
        } catch (\Exception $e) {
            $io->error($e->getMessage());
            return 1;
        }
    }

    private function getBlockData($moduleName, $blockName)
    {
        return $this->blockDataFactory->create([
            'moduleName' => $this->moduleNameFactory->create($moduleName),
            'blockName' => $blockName
        ]);
    }
}