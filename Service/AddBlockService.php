<?php
namespace Wesleywmd\Invent\Service;

use Wesleywmd\Invent\Exception\ModuleServiceException;

class AddBlockService
{
    private $moduleService;

    private $blockClassRenderer;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Php\BlockClassRenderer $blockClassRenderer
    ) {
        $this->moduleService = $moduleService;
        $this->blockClassRenderer = $blockClassRenderer;
    }

    public function execute($moduleName, $blockName)
    {
        $directories = explode("/", $blockName);
        $className = ucfirst(array_pop($directories));
        $directories = array_merge(["Block"], $directories);
        $blockFileName = $className . ".php";
        if( !$this->moduleService->isDirectory($moduleName) ) {
            throw new ModuleServiceException("Cannot Create Block Class. Module directory does not exist.");
        }
        if( $this->moduleService->isFile($moduleName, $blockFileName, $directories) ) {
            throw new ModuleServiceException("Cannot Create Block Class. Block class already exists.");
        }
        $blockString = $this->blockClassRenderer->render($moduleName, $directories, $className);
        $this->moduleService->makeFile($blockFileName, $blockString, $moduleName, $directories);
    }

}