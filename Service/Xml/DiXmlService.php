<?php
namespace Wesleywmd\Invent\Service\Xml;

use Wesleywmd\Invent\Api\Data\PhpClassInterface;

class DiXmlService extends AbstractXmlService
{
    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Xml\XmlHandler $xmlHandler
    ) {
        parent::__construct($moduleService, $xmlHandler);
        $this->fileName = XmlHandler::TYPE_DI;
        $this->fileDirs = ["etc"];
        $this->xmlData = <<<HTML
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xsi:noNamespaceSchemaLocation="urn:magento:framework:ObjectManager/etc/config.xsd">
</config >
HTML;
    }

    public function addConsoleCommand($commandName, PhpClassInterface $phpClass)
    {
        $objectName = $phpClass->getNamespace() . "\\" . $phpClass->getClassName();
        $itemName = str_replace(":", "_", $commandName);
        $xml = $this->loadFile($phpClass->getModule());
        $xpath = "/config";
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "type", "name", "Magento\\Framework\\Console\\CommandList");
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "arguments");
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "argument", "name", "commands");
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "item", "name", $itemName, $objectName);
        $this->xmlHandler->updateAttribute($xml, $xpath, "xsi:type", "object");
        $this->saveFile($phpClass->getModule(), $xml->asXML());
    }
}