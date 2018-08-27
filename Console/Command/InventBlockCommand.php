<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Wesleywmd\Invent\Exception\ModuleServiceException;

class InventBlockCommand extends Command
{
    private $addBlockService;

    public function __construct(
        \Wesleywmd\Invent\Service\AddBlockService $addBlockService
    ) {
        $this->addBlockService = $addBlockService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('invent:block')
            ->setDescription('Create Block')
            ->addArgument("module_name", InputArgument::REQUIRED, "Module Name")
            ->addArgument("block_name", InputArgument::REQUIRED, "Block Name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $moduleName = $input->getArgument("module_name");
            $blockName = $input->getArgument("block_name");
            $this->addBlockService->execute($moduleName, $blockName);
            $output->writeln("{$blockName} Created Successfully!");
        } catch(ModuleServiceException $e) {
            $output->writeln($e->getMessage());
            return 1;
        }
    }

}