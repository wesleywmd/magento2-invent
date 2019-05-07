<?php
namespace Wesleywmd\Invent\Model\ModuleForge;

use Wesleywmd\Invent\Api\Data\XmlFileInterface;

class XmlFile implements XmlFileInterface
{
    private $moduleName;

    private $type;

    private $directories = XmlFileInterface::DEFAULT_DIRECTORIES;

    private $area = XmlFileInterface::DEFAULT_AREA;

    private $dom;

    private $fileNames = [
        XmlFileInterface::TYPE_MODULE => "module.xml",
        XmlFileInterface::TYPE_DI => "di.xml",
        XmlFileInterface::TYPE_CRONTAB => "crontab.xml",
        XmlFileInterface::TYPE_ROUTE => "route.xml"
    ];

    private $fileDomXsds = [
        XmlFileInterface::TYPE_MODULE => XmlFileInterface::DEFAULT_XSD_MODULE,
        XmlFileInterface::TYPE_DI => XmlFileInterface::DEFAULT_XSD_DI,
        XmlFileInterface::TYPE_CRONTAB => XmlFileInterface::DEFAULT_XSD_CRONTAB,
        XmlFileInterface::TYPE_ROUTE => XmlFileInterface::DEFAULT_XSD_ROUTE
    ];

    public function __construct($moduleName, $type, $area = XmlFileInterface::DEFAULT_AREA)
    {
        $this->moduleName = $moduleName;
        $this->type = $type;
        $this->area = $area;
    }

    public function getModule()
    {
        return $this->moduleName;
    }

    public function setModule($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    public function getXmlType()
    {
        return $this->type;
    }

    public function getDirectories()
    {
        if( $this->area === XmlFileInterface::AREA_GLOBAL ) {
            return $this->directories;
        }
        return array_merge($this->directories, [$this->area]);
    }

    public function setDirectories($directories)
    {
        $this->directories = $directories;
        return $this;
    }

    public function getArea()
    {
        return $this->area;
    }

    public function setArea($area)
    {
        $this->area = $area;
        return $this;
    }

    public function getFileName()
    {
        return $this->fileNames[$this->type];
    }

    public function getDefaultDom()
    {
        $xsd = $this->fileDomXsds[$this->type];
        return "<config xmlns:xsi=\"http://www.w3.org/2001/XMLSchema-instance\" xsi:noNamespaceSchemaLocation=\"$xsd\"></config>";
    }
}