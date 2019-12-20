<?php
namespace Wesleywmd\Invent\Model\Component;

abstract class AbstractData
{
    protected $className;
    
    protected $directories;
    
    protected $moduleName;
    
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