<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Model\Block;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class InventBlockCommand extends Command
{
    private $block;

    private $blockDataFactory;

    private $moduleNameFactory;

    public function __construct(Block $block, Block\DataFactory $blockDataFactory, ModuleNameFactory $moduleNameFactory)
    {
        parent::__construct();
        $this->block = $block;
        $this->blockDataFactory = $blockDataFactory;
        $this->moduleNameFactory = $moduleNameFactory;
    }

    protected function configure()
    {
        $this->setName('invent:block')
            ->setDescription('Create Block')
            ->addArgument('moduleName', InputArgument::REQUIRED, 'Module Name')
            ->addArgument('blockName', InputArgument::REQUIRED, 'Block Name');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $blockData = $this->blockDataFactory->create([
                'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName')),
                'blockName' => $input->getArgument('blockName')
            ]);
            $this->block->addToModule($blockData);
            $output->writeln('Block Created Successfully!');
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }
}