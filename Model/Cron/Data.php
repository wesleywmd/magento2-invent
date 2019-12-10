<?php
namespace Wesleywmd\Invent\Model\Cron;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\ModuleName;

class Data implements DataInterface
{
    private $moduleName;

    private $cronName;

    private $method;

    private $schedule;

    private $group;

    private $className;

    private $directories;

    public function __construct(ModuleName $moduleName, $cronName, $method, $schedule, $group)
    {
        $this->moduleName = $moduleName;
        $this->cronName = $cronName;
        $this->method = $method;
        $this->schedule = $schedule;
        $this->group = $group;

        $this->directories = explode('/', $this->cronName);
        $this->className = ucfirst(array_pop($this->directories));
        $this->directories = array_map( function($dir) { return ucfirst($dir); }, $this->directories);
        $this->directories = array_merge(['Cron'], $this->directories);
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getCronName()
    {
        return $this->cronName;
    }

    public function getMethod()
    {
        return $this->method;
    }

    public function getSchedule()
    {
        return $this->schedule;
    }

    public function getGroup()
    {
        return $this->group;
    }

    public function getNamespace()
    {
        return $this->moduleName->getNamespace($this->directories);
    }

    public function getClassName()
    {
        return $this->className;
    }
    
    public function getJobName()
    {
        return $this->moduleName->getSlug(array_merge($this->directories,[$this->cronName]));
    }
    
    public function getInstance()
    {
        return $this->moduleName->getNamespace(array_merge($this->directories,[$this->className]));
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function getPathPieces()
    {
        return array_merge($this->directories, [$this->className.'.php']);
    }
}