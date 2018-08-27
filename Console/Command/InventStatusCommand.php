<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class InventStatusCommand extends Command
{
    private $moduleService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService
    ) {
        $this->moduleService = $moduleService;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName('invent:status')
            ->setDescription('Get Module Status')
            ->addArgument("module_name", InputArgument::REQUIRED, "Module Name");
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $name = $input->getArgument("module_name");
        print_r([
            "name"             => $name,
            "directory"        => $this->moduleService->getDirectory($name),
            "enabled"          => $this->moduleService->isEnabled($name),
            "directory_exists" => $this->moduleService->isDirectory($name),
            "registered"       => $this->moduleService->isRegistered($name),
            "is_composer"      => $this->moduleService->isComposer($name),
            "is_custom"        => $this->moduleService->isCustom($name),
            "module"           => $this->moduleService->get($name)
        ]);
    }

}