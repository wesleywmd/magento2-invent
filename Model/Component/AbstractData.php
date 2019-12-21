<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Model\ModuleName;

abstract class AbstractData
{
    protected $className;

    protected $directories;

    /** @var ModuleName $moduleName */
    protected $moduleName;

    public function __construct(ModuleName $moduleName, $className, $directories)
    {
        $this->moduleName = $moduleName;
        $this->className = $className;
        $this->directories = $directories;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function getInstance()
    {
        return $this->moduleName->getNamespace(array_merge($this->directories,[$this->className]));
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getNamespace()
    {
        return $this->moduleName->getNamespace($this->directories);
    }

    public function getPath()
    {
        return $this->moduleName->getPath(array_merge($this->directories, [$this->className.'.php']));
    }
}