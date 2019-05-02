<?php
namespace Wesleywmd\Invent\Service\Xml;

class CrontabXmlService extends AbstractXmlService
{

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Xml\XmlHandler $xmlHandler
    ) {
        parent::__construct($moduleService, $xmlHandler);
        $this->fileName = XmlHandler::TYPE_CRONTAB;
        $this->fileDirs = ["etc"];
        $this->xmlData = <<<HTML
<config xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" 
    xsi:noNamespaceSchemaLocation="urn:magento:module:Magento_Cron:etc/crontab.xsd">
</config >
HTML;
    }

    public function addJob($moduleName, $instance, $method, $schedule, $group)
    {
        $xml = $this->loadFile($moduleName);
        $jobName = trim(strtolower(str_replace("\\", "_", $instance)), "_");

        $xpath = "/config";
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "group", "id", $group);
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "job", "name", $jobName);
        $this->xmlHandler->updateAttribute($xml, $xpath, "instance", $instance);
        $this->xmlHandler->updateAttribute($xml, $xpath, "method", $method);
        $xpath = $this->xmlHandler->loadElementIfNotExists($xml, $xpath, "schedule", null, null, $schedule);

        $this->saveFile($moduleName, $xml->asXML());
    }

}