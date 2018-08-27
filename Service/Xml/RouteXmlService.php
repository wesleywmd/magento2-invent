<?php
namespace Wesleywmd\Invent\Service\Xml;

class RouteXmlService extends AbstractXmlService
{

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Xml\XmlHandler $xmlHandler
    ) {
        parent::__construct($moduleService, $xmlHandler);
        $this->fileName = XmlHandler::TYPE_ROUTE;
        $this->fileDirs = ["etc", "frontend"];
        $this->xmlData = <<<HTML
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xsi:noNamespaceSchemaLocation="urn:magento:framework:App/etc/routes.xsd">
</config >
HTML;
    }

    public function addController($moduleName, $routerId, $frontName)
    {
        $xml = $this->loadFile($moduleName);

        $xpath = "/config";
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "router", "id", $routerId);
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "route", "id", $frontName);
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "module", "name", $moduleName);

        $this->saveFile($moduleName, $xml->asXML());
    }

}