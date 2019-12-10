<?php
namespace Wesleywmd\Invent\Model\XmlParser;

use Magento\Framework\Filesystem\DirectoryList;
use Wesleywmd\Invent\Helper\PathHelper;

class Location
{
    const AREA_GLOBAL = 'global';
    const AREA_FRONTEND = 'frontend';
    const AREA_ADMINHTML = 'adminhtml';

    const TYPE_MODULE = 'module';
    const TYPE_DI = 'di';
    const TYPE_CRONTAB = 'crontab';
    const TYPE_ROUTE = 'route';
    const TYPE_DB_SCHEMA = 'db_schema';

    private $pathHelper;

    private $validAreas = [
        self::AREA_FRONTEND, 
        self::AREA_ADMINHTML, 
        self::AREA_GLOBAL
    ];
    
    private $validTypes = [
        self::TYPE_MODULE, 
        self::TYPE_DI, 
        self::TYPE_CRONTAB, 
        self::TYPE_ROUTE, 
        self::TYPE_DB_SCHEMA
    ];

    public function __construct(PathHelper $pathHelper)
    {
        $this->pathHelper = $pathHelper;
    }

    public function getPath($moduleName, $type, $area)
    {
        $this->validateType($type);
        $this->validateArea($area);
        $directories = $this->getDirectories($area);
        return $this->pathHelper->fullPath($moduleName, array_merge($directories, [$type.'.xml']));
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