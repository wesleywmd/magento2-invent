<?php
namespace Wesleywmd\Invent\Service;

use Wesleywmd\Invent\Exception\ModuleServiceException;

class AddCronService
{
    private $moduleService;

    private $cronClassRenderer;

    private $crontabXmlService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Php\CronClassRenderer $cronClassRenderer,
        \Wesleywmd\Invent\Service\Xml\CrontabXmlService $crontabXmlService
    ) {
        $this->moduleService = $moduleService;
        $this->cronClassRenderer = $cronClassRenderer;
        $this->crontabXmlService = $crontabXmlService;
    }

    public function execute($moduleName, $cronName, $method, $schedule, $group)
    {
        $directories = explode("/", $cronName);
        $className = ucfirst(array_pop($directories));
        $directories = array_map( function($dir) { return ucfirst($dir); }, $directories);
        $directories = array_merge(["Cron"], $directories);
        $cronFileName = $className . ".php";
        $instance = $this->moduleService->getNamespace($moduleName, $directories, $className);
        if( !$this->moduleService->isDirectory($moduleName) ) {
            throw new ModuleServiceException("Cannot Create Cron Class. Module directory does not exist.");
        }
        if( $this->moduleService->isFile($moduleName, $cronFileName, $directories) ) {
            throw new ModuleServiceException("Cannot Create Cron Class. Cron class already exists.");
        }
        $cronString = $this->cronClassRenderer->render($moduleName, $directories, $className, $method);
        $this->moduleService->makeFile($cronFileName, $cronString, $moduleName, $directories);
        $this->crontabXmlService->addJob($moduleName, $instance, $method, $schedule, $group);
    }

}