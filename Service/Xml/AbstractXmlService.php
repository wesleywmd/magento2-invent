<?php
namespace Wesleywmd\Invent\Service\Xml;

abstract class AbstractXmlService
{
    protected $moduleService;
    protected $xmlHandler;

    protected $fileName = "";
    protected $fileDirs = [];
    protected $xmlData = "";

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService,
        \Wesleywmd\Invent\Service\Xml\XmlHandler $xmlHandler
    ) {
        $this->moduleService = $moduleService;
        $this->xmlHandler = $xmlHandler;
    }

    protected function loadFile($moduleName)
    {
        if( $this->moduleService->isFile($moduleName, $this->fileName, $this->fileDirs) ) {
            return $this->xmlHandler->loadFileContents($this->fileName, $moduleName, $this->fileDirs);
        } else {
            return new \SimpleXMLElement($this->xmlData);
        }
    }

    protected function saveFile($moduleName, $xml)
    {
        $outputXml = $this->xmlHandler->reformatXmlString($xml);
        $this->moduleService->makeFile($this->fileName, $outputXml, $moduleName, $this->fileDirs);
    }

}