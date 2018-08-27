<?php
namespace Wesleywmd\Invent\Service\Xml;

class ModuleXmlService extends AbstractXmlService
{

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Xml\XmlHandler $xmlHandler
    ) {
        parent::__construct($moduleService, $xmlHandler);
        $this->fileName = XmlHandler::TYPE_MODULE;
        $this->fileDirs = ["etc"];
        $this->xmlData = <<<HTML
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xsi:noNamespaceSchemaLocation="urn:magento:framework:Module/etc/module.xsd">
</config>
HTML;
    }

    public function registerModule($moduleName, $version)
    {
        $xml = $this->loadFile($moduleName);
        $xpath = "/config";
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "module", "name", $moduleName);
        $this->xmlHandler->updateAttribute($xml, $xpath, "setup_version", $version);
        $this->saveFile($moduleName, $xml->asXML());
    }

}