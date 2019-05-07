<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\Data\DomInterface;
use Wesleywmd\Invent\Api\Data\PhpClassInterface;
use Wesleywmd\Invent\Api\Data\XmlClassInterface;
use Wesleywmd\Invent\Api\Data\XmlFileInterface;
use Wesleywmd\Invent\Model\ModuleForge\PhpClassFactory;
use Wesleywmd\Invent\Model\ModuleForge\XmlFile\Dom;

class ModuleForge
{
    private $phpClassRender;

    private $phpClassFactory;

    private $moduleHelper;

    private $xmlFileFactory;

    public function __construct(
        \Wesleywmd\Invent\Model\ModuleForge\PhpClassFactory $phpClassFactory,
        \Wesleywmd\Invent\Model\ModuleForge\PhpClass\Renderer $phpClassRenderer,
        \Wesleywmd\Invent\Model\ModuleForge\XmlFileFactory $xmlFileFactory,
        \Wesleywmd\Invent\Model\ModuleForge\XmlFile\DomFactory $xmlDomFactory,
        \Wesleywmd\Invent\Helper\ModuleHelper $moduleHelper

    ) {
        $this->phpClassRender = $phpClassRenderer;
        $this->phpClassFactory = $phpClassFactory;
        $this->xmlFileFactory = $xmlFileFactory;
        $this->moduleHelper = $moduleHelper;
        $this->xmlDomFactory = $xmlDomFactory;
    }

    public function addModule($moduleName)
    {
        if( is_dir($this->moduleHelper->getDirectoryPath($moduleName)) ) {
            throw new \Exception("Cannot Create Module, directory already exists.");
        }

        $registrationString = implode("\n", [
            "<?php",
            "   \\Magento\\Framework\\Component\\ComponentRegistrar::register(",
            "   \\Magento\\Framework\\Component\\ComponentRegistrar::MODULE,",
            "   \"{$moduleName}\",",
            "__DIR__",
            ");"
        ]);
        $this->moduleHelper->makePhpFile($moduleName, $registrationString, "registration.php");

        /** @var XmlFileInterface $moduleXml */
        $moduleXml = $this->xmlFileFactory->create(["moduleName"=>$moduleName, "type"=>XmlFileInterface::TYPE_MODULE]);
        /** @var DomInterface $moduleXmlDom */
        $moduleXmlDom = $this->xmlDomFactory->create(["xmlFile"=>$moduleXml]);
        $moduleXmlDom->updateElement("module", "name", $moduleName);
        $moduleXmlDom->updateAttribute("setup_version", "0.0.1", ["module[@name=\"$moduleName\"]"]);
        $this->moduleHelper->makeXmlFile($moduleXml, $moduleXmlDom);
    }

    public function addPreference($moduleName, $for, $type, $area = XmlFileInterface::AREA_GLOBAL)
    {
        /** @var XmlClassInterface $diXml */
        $diXml = $this->xmlFileFactory->create(["moduleName"=>$moduleName, "type"=>XmlFileInterface::TYPE_DI, "area"=>$area]);
        /** @var DomInterface $diXmlDom */
        $diXmlDom = $this->xmlDomFactory->create(["xmlFile"=>$diXml]);
        $diXmlDom->updateElement("preference", "for", $for);
        $diXmlDom->updateAttribute("type", $type, ["preference[@for=\"$for\"]"]);
        $this->moduleHelper->makeXmlFile($diXml, $diXmlDom);
    }

    public function addModel($moduleName, $modelName, $columns = [])
    {
        // create interface
        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $modelInterface */
        $modelInterface = $this->phpClassFactory->create(["moduleName"=>$moduleName]);
        $modelInterface->setClassName($modelName . "Interface")
            ->setDirectories(["Api", "Data"])
            ->addField("DB_MAIN_TABLE", PhpClassInterface::PRIV_CONST, strtolower($moduleName."_".$modelName))
            ->addField("ENTITY_ID", PhpClassInterface::PRIV_CONST, "entity_id")
            ->addGetterMethod($modelInterface->getInstance(true), "entity_id")
            ->addSetterMethod($modelInterface->getInstance(true), "entity_id");
        foreach( $columns as $column ) {
            $modelInterface->addField(strtoupper($column), PhpClassInterface::PRIV_CONST, $column)
                ->addGetterMethod($modelInterface->getInstance(true), $column)
                ->addSetterMethod($modelInterface->getInstance(true), $column);
        }
        $this->moduleHelper->makePhpInterface($modelInterface);

        // create model
        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $model */
        $model = $this->phpClassFactory->create(["moduleName"=>$moduleName]);
        $model->setClassName($modelName)
            ->setDirectories(["Model"])
            ->setExtends("\\Magento\\Framework\\Model\\AbstractModel")
            ->setImplements($modelInterface->getInstance(true))
            ->addMethod("_construct", PhpClassInterface::PRIV_PROTECTED, [], [
                "\$this->_init(ResourceModel\\$modelName::class);"
            ]);
        foreach( $columns as $column ) {
            $model->addGetterMethod($modelInterface->getInstance(true), $column)
                ->addSetterMethod($modelInterface->getInstance(true), $column);
        }
        $this->moduleHelper->makePhpClass($model);

        // create resource model
        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $resourceModel */
        $resourceModel = $this->phpClassFactory->create(["moduleName"=>$moduleName]);
        $resourceModel->setClassName($modelName)
            ->setDirectories(["Model", "ResourceModel"])
            ->setExtends("\\Magento\\Framework\\Model\\ResourceModel\\Db\\AbstractDb")
            ->addMethod("_construct", PhpClassInterface::PRIV_PROTECTED, [], [
                "\$this->_init({$modelInterface->getInstance(true)}::DB_MAIN_TABLE, {$modelInterface->getInstance(true)}::ENTITY_ID);"
            ]);
        $this->moduleHelper->makePhpClass($resourceModel);

        // create collection
        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $collection */
        $collection = $this->phpClassFactory->create(["moduleName"=>$moduleName]);
        $collection->setClassName("Collection")
            ->setDirectories(["Model", "ResourceModel", ucfirst($modelName)])
            ->setExtends("\\Magento\\Framework\\Model\\ResourceModel\\Db\\Collection\\AbstractCollection")
            ->addField("_idFieldName", PhpClassInterface::PRIV_PROTECTED, "entity_id")
            ->addField("_eventPrefix", PhpClassInterface::PRIV_PROTECTED, strtolower($moduleName."_".$modelName)."_collection")
            ->addField("_eventObject", PhpClassInterface::PRIV_PROTECTED, strtolower($modelName)."_collection")
            ->addMethod("_construct", PhpClassInterface::PRIV_PROTECTED, [], [
                "\$this->_init({$model->getInstance(true)}::class, {$resourceModel->getInstance(true)}::class);"
            ]);
        $this->moduleHelper->makePhpClass($collection);

        // create search result interface
        // create search result
        // create repository interface
        // create repository
        // create set up script
        // register model interface preference
        $this->addPreference($moduleName, $modelInterface->getInstance(), $model->getInstance());
        // register repository interface preference
    }

    public function addCron($moduleName, $cronName, $method, $schedule, $group)
    {
        $directories = explode("/", $cronName);
        $className = ucfirst(array_pop($directories));
        $directories = array_map( function($dir) { return ucfirst($dir); }, $directories);
        $directories = array_merge(["Cron"], $directories);

        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $cron */
        $cron = $this->phpClassFactory->create(["moduleName"=>$moduleName]);
        $cron->setClassName($className)
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

        $this->moduleHelper->makePhpClass($cron);

        /** @var XmlClassInterface $crontabXml */
        $crontabXml = $this->xmlFileFactory->create(["moduleName"=>$moduleName, "type"=>XmlFileInterface::TYPE_CRONTAB]);
        /** @var DomInterface $crontabXmlDom */
        $crontabXmlDom = $this->xmlDomFactory->create(["xmlFile"=>$crontabXml]);
        $jobName = strtolower(str_replace("\\", "_", $cron->getInstance()));
        $crontabXmlDom->updateElement("group", "id", $group)
            ->updateElement("job", "name", $jobName, null, ["group[@id=\"$group\"]"])
            ->updateAttribute("instance", $cron->getInstance(), ["group[@id=\"$group\"]"])
            ->updateAttribute("method", $method, ["group[@id=\"$group\"]"])
            ->updateElement("schedule", null, null, $schedule, ["group[@id=\"$group\"]", "job[@name=\"$jobName\"]"]);
        $this->moduleHelper->makeXmlFile($crontabXml, $crontabXmlDom);
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
        $controller = $this->phpClassFactory->create(["moduleName"=>$moduleName]);
        $controller->setClassName($className)
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

        $this->moduleHelper->makePhpClass($controller);

        /** @var XmlClassInterface $routeXml */
        $routeXml = $this->xmlFileFactory->create(["moduleName"=>$moduleName, "type"=>XmlFileInterface::TYPE_ROUTE, "area"=>"frontend"]);
        /** @var DomInterface $routeXmlDom */
        $routeXmlDom = $this->xmlDomFactory->create(["xmlFile"=>$routeXml]);
        $routeXmlDom->updateElement("router", "id", $routerId)
            ->updateElement( "route", "id", $frontName, null, ["router[@id=\"$routerId\"]"])
            ->updateElement("module", "name", $moduleName, null, ["router[@id=\"$routerId\"]", "route[@id=\"$frontName\"]"]);
        $this->moduleHelper->makeXmlFile($routeXml, $routeXmlDom);
    }

    public function addBlock($moduleName, $blockName)
    {
        $directories = explode("/", $blockName);
        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $block */
        $block = $this->phpClassFactory->create(["moduleName"=>$moduleName]);
        $block->setClassName(ucfirst(array_pop($directories)))
            ->setDirectories(array_merge(["Block"], $directories))
            ->setExtends("\\Magento\\Framework\\View\\Element\\Template");
        $this->moduleHelper->makePhpClass($block);
    }

    public function addCommand($moduleName, $commandName)
    {
        /** @var \Wesleywmd\Invent\Model\ModuleForge\phpClass $command */
        $command = $this->phpClassFactory->create(["moduleName"=>$moduleName]);
        $command->setDirectories(["Console", "Command"])
            ->setClassName(implode("", array_map("ucfirst", explode(":", $commandName))) . "Command")
            ->setExtends("\\Symfony\\Component\\Console\\Command\\Command")
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
        $this->moduleHelper->makePhpClass($command);

        $itemName = str_replace(":", "_", $commandName);
        /** @var XmlClassInterface $diXml */
        $diXml = $this->xmlFileFactory->create(["moduleName"=>$moduleName, "type"=>XmlFileInterface::TYPE_DI]);
        /** @var DomInterface $routeXmlDom */
        $diXmlDom = $this->xmlDomFactory->create(["xmlFile"=>$diXml]);
        $diXmlDom->updateElement("type", "name", "Magento\\Framework\\Console\\CommandList")
            ->updateElement( "arguments", null, null, null, ["type[@name=\"Magento\\Framework\\Console\\CommandList\"]"])
            ->updateElement("argument", "name", "commands", null, ["type[@name=\"Magento\\Framework\\Console\\CommandList\"]", "arguments"])
            ->updateElement("item", "name", $itemName, $command->getInstance(), ["type[@name=\"Magento\\Framework\\Console\\CommandList\"]", "arguments", "argument[@name=\"commands\"]"])
            ->updateAttribute("xsi:type", "object", ["type[@name=\"Magento\\Framework\\Console\\CommandList\"]", "arguments", "argument[@name=\"commands\"]", "item[@name=\"$itemName\"]"]);
        $this->moduleHelper->makeXmlFile($diXml, $diXmlDom);
    }
}