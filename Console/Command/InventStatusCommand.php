<?php
namespace Wesleywmd\Invent\Console\Command;

use Symfony\Component\Console\Input\InputArgument;

class InventStatusCommand extends \Symfony\Component\Console\Command\Command
{
    private $moduleHelper;

    public function __construct(
        \Wesleywmd\Invent\Helper\ModuleHelper $moduleHelper
    ) {
        $this->moduleHelper = $moduleHelper;
        parent::__construct();
    }

    protected function configure()
    {
        $this->setName("invent:status")
            ->setDescription("Get Module Status")
            ->addArgument("module_name", \Symfony\Component\Console\Input\InputArgument::REQUIRED, "Module Name");
    }

    protected function execute(
        \Symfony\Component\Console\Input\InputInterface $input,
        \Symfony\Component\Console\Output\OutputInterface $output
    ) {
        $moduleName = $input->getArgument("module_name");
        print_r([
            "name"             => $moduleName,
            "directory"        => $this->moduleHelper->getDirectoryPath($moduleName),
            "enabled"          => $this->moduleHelper->isEnabled($moduleName),
            "directory_exists" => is_dir($this->moduleHelper->getDirectoryPath($moduleName)),
            "registered"       => $this->moduleHelper->isRegistered($moduleName),
            "is_composer"      => $this->moduleHelper->isComposer($moduleName),
            "is_custom"        => $this->moduleHelper->isCustom($moduleName),
            "module"           => $this->moduleHelper->get($moduleName)
        ]);
    }

}