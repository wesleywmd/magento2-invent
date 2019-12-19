<?php
namespace Wesleywmd\Invent\Model\Logger;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\ModuleName;

class Data implements DataInterface
{
    private $moduleName;

    private $loggerName;

    private $fileName;

    private $type;

    public function __construct(ModuleName $moduleName, $loggerName, $fileName = null, $type = 'INFO')
    {
        $this->moduleName = $moduleName;
        $this->loggerName = $loggerName;
        $this->fileName = $fileName;
        $this->type = $type;
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getLoggerName()
    {
        return $this->moduleName->getSlug([$this->loggerName]);
    }

    public function getFileName()
    {
        if (is_null($this->fileName)) {
            return '/var/log/'.str_replace('_', '/', $this->moduleName->getSlug()).'.log';
        }
        return '/var/log/'.$this->fileName;
    }

    public function getType()
    {
        return $this->type;
    }
    
    public function getPath($file)
    {
        return $this->moduleName->getPath(['Logger', $file]);
    }
}