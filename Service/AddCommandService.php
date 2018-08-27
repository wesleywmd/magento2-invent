<?php
namespace Wesleywmd\Invent\Service;

use Wesleywmd\Invent\Exception\ModuleServiceException;

class AddCommandService
{
    private $moduleService;

    private $commandClassRenderer;

    private $diXmlService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Php\CommandClassRenderer $commandClassRenderer,
        \Wesleywmd\Invent\Service\Xml\DiXmlService $diXmlService
    ) {
        $this->moduleService = $moduleService;
        $this->commandClassRenderer = $commandClassRenderer;
        $this->diXmlService = $diXmlService;
    }

    public function execute($moduleName, $commandName)
    {
        if( !$this->moduleService->isDirectory($moduleName) ) {
            throw new ModuleServiceException("Cannot Create Console Command. Module directory does not exist.");
        }
        $commandFileName = $this->commandClassRenderer->getClassName($commandName) . ".php";
        if( $this->moduleService->isFile($moduleName, $commandFileName, [ "Console", "Command" ]) ) {
            throw new ModuleServiceException("Cannot Create Console Command. Command class already exists.");
        }
        $commandFileName = $this->commandClassRenderer->getClassName($commandName) . ".php";
        $commandString = $this->commandClassRenderer->render($moduleName, $commandName);
        $this->moduleService->makeFile($commandFileName, $commandString, $moduleName, [ "Console", "Command" ]);
        $this->diXmlService->addConsoleCommand($moduleName, $commandName);
    }

}