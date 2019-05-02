<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\Data\PhpClassInterface;
use Wesleywmd\Invent\Model\ModuleForge\PhpClassFactory;

class ModuleForge
{
    private $moduleService;

    private $phpClassRender;

    private $phpClassFactory;

    private $diXmlService;

    private $routeXmlService;

    private $crontabXmlService;

    private $moduleXmlService;

    private $moduleForgeHelper;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Model\ModuleForge\PhpClassFactory $phpClassFactory,
        \Wesleywmd\Invent\Model\ModuleForge\PhpClass\Renderer $phpClassRenderer,
        \Wesleywmd\Invent\Service\Xml\DiXmlService $diXmlService,
        \Wesleywmd\Invent\Service\Xml\RouteXmlService $routeXmlService,
        \Wesleywmd\Invent\Service\Xml\CrontabXmlService $crontabXmlService,
        \Wesleywmd\Invent\Service\Xml\ModuleXmlService $moduleXmlService,
        \Wesleywmd\Invent\Helper\ModuleForgeHelper $moduleForgeHelper
    ) {
        $this->moduleService = $moduleService;
        $this->phpClassRender = $phpClassRenderer;
        $this->phpClassFactory = $phpClassFactory;
        $this->diXmlService = $diXmlService;
        $this->routeXmlService = $routeXmlService;
        $this->crontabXmlService = $crontabXmlService;
        $this->moduleXmlService = $moduleXmlService;
        $this->moduleForgeHelper = $moduleForgeHelper;
    }

    public function addModule($moduleName)
    {
        if( $this->moduleService->isDirectory($moduleName) ) {
            throw new \Exception("Cannot Create Module, directory already exists.");
        }
        $registrationString = $this->moduleForgeHelper->getRegistrationPhpContent($moduleName);
        $this->moduleService->makeFile("registration.php", $registrationString, $moduleName);
        $this->moduleXmlService->registerModule($moduleName, "0.0.1");
    }

    public function addCron($moduleName, $cronName, $method, $schedule, $group)
    {
        $directories = explode("/", $cronName);
        $className = ucfirst(array_pop($directories));
        $directories = array_map( function($dir) { return ucfirst($dir); }, $directories);
        $directories = array_merge(["Cron"], $directories);

        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $cron */
        $cron = $this->phpClassFactory->create();
        $cron->setModule($moduleName)
            ->setClassName($className)
            ->setDirectories($directories);
        $cron->addField("logger", PhpClassInterface::PRIV_PROTECTED)
            ->addMethod("__construct", PhpClassInterface::PRIV_PUBLIC, [
                "logger" => ["type" => "\\Psr\\Log\\LoggerInterface"]
            ], [
                "\$this->logger = \$logger;"
            ])
            ->addMethod("execute", PhpClassInterface::PRIV_PUBLIC, [], [
                "\$this->logger->info(__METHOD__);",
                "// @TODO implement {$method} method for {$cron->getInstance()}",
                "return \$this;"
            ]);

        $this->addPhpClass($cron);

        $this->crontabXmlService->addJob($moduleName, $cron->getInstance(), $method, $schedule, $group);
    }

    public function addController($moduleName, $controllerUrl, $routerId)
    {
        $directories = array_reverse(explode("/", $controllerUrl));
        $frontName = array_pop($directories);
        $directories = array_reverse($directories);
        $className = ucfirst(array_pop($directories));
        $directories = array_map( function($dir) { return ucfirst($dir); }, $directories);
        $directories = array_merge(["Controller"], $directories);

        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $controller */
        $controller = $this->phpClassFactory->create();
        $controller->setModule($moduleName)
            ->setClassName($className)
            ->setDirectories($directories)
            ->setExtends("\\Magento\\Framework\\App\\Action\\Action")
            ->addField("resultPageFactory", PhpClassInterface::PRIV_PROTECTED)
            ->addMethod("__construct", PhpClassInterface::PRIV_PUBLIC, [
                "context" => ["type" => "\\Magento\\Framework\\App\\Action\\Context"],
                "resultPageFactory" => ["type" => "\\Magento\\Framework\\View\\Result\\PageFactory"]
            ], [
                "\$this->resultPageFactory = \$resultPageFactory;",
                "parent::__construct(\$context);"
            ])
            ->addMethod("execute", PhpClassInterface::PRIV_PUBLIC, [], [
                "\$resultPage = \$this->resultPageFactory->create();",
                "return \$resultPage;"
            ]);

        $this->addPhpClass($controller);

        $this->routeXmlService->addController($moduleName, $routerId, $frontName);
    }

    public function addBlock($moduleName, $blockName)
    {
        $directories = explode("/", $blockName);

        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $block */
        $block = $this->phpClassFactory->create();
        $block->setModule($moduleName)
            ->setClassName(ucfirst(array_pop($directories)))
            ->setDirectories(array_merge(["Block"], $directories))
            ->setExtends("\\Magento\\Framework\\View\\Element\\Template");

        $this->addPhpClass($block);
    }

    public function addCommand($moduleName, $commandName)
    {
        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $command */
        $command = $this->phpClassFactory->create();
        $command->setModule($moduleName)
            ->setDirectories(["Console", "Command"])
            ->setClassName(implode("", array_map("ucfirst", explode(":", $commandName))) . "Command")
            ->setExtends("Symfony\\Component\\Console\\Command\\Command")
            ->addUseStatement("Symfony\\Component\\Console\\Input\\InputInterface")
            ->addUseStatement("Symfony\\Component\\Console\\Output\\OutputInterface")
            ->addMethod("configure", PhpClassInterface::PRIV_PROTECTED, [], [
                "\$this->setName(\"{$commandName}\");",
                 "// @TODO implement configure method for {$commandName}"
            ])
            ->addMethod("execute", PhpClassInterface::PRIV_PROTECTED, [
                "input" => ["type"=>"InputInterface"],
                "output" => ["type"=>"OutputInterface"]
            ], [
                "// @TODO implement execute method for {$commandName}"
            ]);

        $this->addPhpClass($command);

        $this->diXmlService->addConsoleCommand($commandName, $command);
    }

    private function addPhpClass(PhpClassInterface $phpClass)
    {
        if( ! $this->moduleService->isDirectory($phpClass->getModule()) ) {
            throw new \Exception("Specified Module doesn't exist.");
        }
        if( $this->moduleService->isFile($phpClass->getModule(), $phpClass->getFileName(), $phpClass->getDirectories()) ) {
            throw new \Exception("Cannot create specified class. Class already exists.");
        }

        $filePath = $this->moduleService->getDirectory($phpClass->getModule(), $phpClass->getDirectories());
        if( !is_dir($filePath) ) {
            mkdir($filePath, 0777, true);
        }
        $filePath .= DIRECTORY_SEPARATOR . $phpClass->getFileName();
        $contents = $this->phpClassRender->PhpClassToString($phpClass);

        $handle = fopen($filePath, "w") or die("Cannot open file:  " . $filePath);
        fwrite($handle, $contents);
        fclose($handle);

    }
}