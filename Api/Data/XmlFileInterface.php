<?php
namespace Wesleywmd\Invent\Api\Data;

interface XmlFileInterface
{
    const DEFAULT_DIRECTORIES = ["etc"];
    const DEFAULT_XPATH = "/config";
    const DEFAULT_AREA = self::AREA_GLOBAL;

    const TYPE_MODULE = "module";
    const TYPE_DI = "di";
    const TYPE_CRONTAB = "crontab";
    const TYPE_ROUTE = "route";

    const AREA_GLOBAL = "global";
    const AREA_FRONTEND = "frontend";
    const AREA_ADMINHTML = "adminhtml";
    const AREA_CRONTAB = "crontab";

    const DEFAULT_XSD_MODULE = "urn:magento:framework:Module/etc/module.xsd";
    const DEFAULT_XSD_DI = "urn:magento:framework:ObjectManager/etc/config.xsd";
    const DEFAULT_XSD_CRONTAB = "urn:magento:module:Magento_Cron:etc/crontab.xsd";
    const DEFAULT_XSD_ROUTE = "urn:magento:framework:App/etc/routes.xsd";

    public function getFileName();
    public function getDirectories();
    public function setDirectories($directories);
    public function getModule();
    public function setModule($moduleName);
    public function getXmlType();
    public function getArea();
    public function setArea($area);
    public function getDefaultDom();
}