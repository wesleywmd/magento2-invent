<?php
namespace Wesleywmd\Invent\Service\Xml;

class DiXmlService extends AbstractXmlService
{
    private $commandClassRenderer;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Xml\XmlHandler $xmlHandler,
        \Wesleywmd\Invent\Service\Php\CommandClassRenderer $commandClassRenderer
    ) {
        $this->commandClassRenderer = $commandClassRenderer;
        parent::__construct($moduleService, $xmlHandler);
        $this->fileName = XmlHandler::TYPE_DI;
        $this->fileDirs = ["etc"];
        $this->xmlData = <<<HTML
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
</config >
HTML;
    }

    public function addConsoleCommand($moduleName, $commandName)
    {
        $objectName = $this->commandClassRenderer->getNamespace($moduleName, $commandName);
        $itemName = str_replace(":", "_", $commandName);
        $xml = $this->loadFile($moduleName);
        $xpath = "/config";
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "type", "name", "Magento\\Framework\\Console\\CommandList");
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "arguments");
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "argument", "name", "commands");
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "item", "name", $itemName, $objectName);
        $this->xmlHandler->updateAttribute($xml, $xpath, "xsi:type", "object");
        $this->saveFile($moduleName, $xml->asXML());
    }

}