<?php
namespace Wesleywmd\Invent\Service;

use Wesleywmd\Invent\Exception\ModuleServiceException;

class AddControllerService
{
    private $moduleService;

    private $controllerClassRenderer;

    private $routeXmlService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Php\ControllerClassRenderer $controllerClassRenderer,
        \Wesleywmd\Invent\Service\Xml\RouteXmlService $routeXmlService
    ) {
        $this->moduleService = $moduleService;
        $this->controllerClassRenderer = $controllerClassRenderer;
        $this->routeXmlService = $routeXmlService;
    }

    public function execute($moduleName, $controllerUrl, $routerId)
    {
        $directories = array_reverse(explode("/", $controllerUrl));
        $frontName = array_pop($directories);
        $directories = array_reverse($directories);
        $className = ucfirst(array_pop($directories));
        $directories = array_map( function($dir) { return ucfirst($dir); }, $directories);
        $directories = array_merge(["Controller"], $directories);
        $controllerFileName = $className . ".php";
        if( !$this->moduleService->isDirectory($moduleName) ) {
            throw new ModuleServiceException("Cannot Create Controller Class. Module directory does not exist.");
        }
        if( $this->moduleService->isFile($moduleName, $controllerFileName, $directories) ) {
            throw new ModuleServiceException("Cannot Create Controller Class. Controller class already exists.");
        }
        $controllerString = $this->controllerClassRenderer->render($moduleName, $directories, $className);
        $this->moduleService->makeFile($controllerFileName, $controllerString, $moduleName, $directories);
        $this->routeXmlService->addController($moduleName, $routerId, $frontName);
    }

}