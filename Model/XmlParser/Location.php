<?php
namespace Wesleywmd\Invent\Model\XmlParser;

use Magento\Framework\Filesystem\DirectoryList;
use Wesleywmd\Invent\Model\ModuleName;

class Location
{
    const AREA_GLOBAL = 'global';
    const AREA_FRONTEND = 'frontend';
    const AREA_ADMINHTML = 'adminhtml';
    const AREA_VIEW = 'view';

    const TYPE_MODULE = 'module';
    const TYPE_DI = 'di';
    const TYPE_CRONTAB = 'crontab';
    const TYPE_ROUTE = 'route';
    const TYPE_DB_SCHEMA = 'db_schema';
    const TYPE_ACL = 'acl';
    const TYPE_MENU = 'menu';
    const TYPE_SYSTEM = 'system';
    const TYPE_LAYOUT = 'layout';
    const TYPE_LISTING = 'listing';
    const TYPE_FORM = 'form';

    private $validAreas = [
        self::AREA_FRONTEND,
        self::AREA_ADMINHTML,
        self::AREA_GLOBAL,
        self::AREA_VIEW
    ];

    private $validTypes = [
        self::TYPE_MODULE,
        self::TYPE_DI,
        self::TYPE_CRONTAB,
        self::TYPE_ROUTE,
        self::TYPE_DB_SCHEMA,
        self::TYPE_ACL,
        self::TYPE_MENU,
        self::TYPE_SYSTEM,
        self::TYPE_LAYOUT
    ];

    public function getPath(ModuleName $moduleName, $type, $area)
    {
        $this->validateType($type);
        $this->validateArea($area);
        $directories = $this->getDirectories($area);
        return $moduleName->getPath(array_merge($directories, [$type.'.xml']));
    }

    private function getDirectories($area)
    {
        $directories = ['etc'];
        if (in_array($area, [self::AREA_FRONTEND, self::AREA_ADMINHTML])) {
            $directories[] = $area;
        }
        return $directories;
    }

    private function validateType($type)
    {
        if (!in_array($type, $this->validTypes)) {
            throw new \Exception('Invalid XML Type provided: '.$type);
        }
    }

    private function validateArea($area)
    {
        if (!in_array($area, $this->validAreas) || is_null($area)) {
            throw new \Exception('Invalid XML Area provided: '.$area);
        }
    }
}