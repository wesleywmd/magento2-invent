<?php
namespace Wesleywmd\Invent\Model\Controller;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\ModuleName;

class Data implements DataInterface
{
    private $moduleName;

    private $controllerUrl;

    private $router;

    private $directories;

    private $frontName;

    private $className;

    public function __construct(ModuleName $moduleName, $controllerUrl, $router)
    {
        $this->moduleName = $moduleName;
        $this->controllerUrl = $controllerUrl;
        $this->router = $router;
        $this->directories = array_reverse(explode('/', $this->controllerUrl));
        $this->frontName = array_pop($this->directories);
        $this->directories = array_reverse($this->directories);
        $this->className = ucfirst(array_pop($this->directories));
        $this->directories = array_map( function($dir) { return ucfirst($dir); }, $this->directories);
        $this->directories = array_merge(['Controller'], $this->directories);
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getControllerUrl()
    {
        return $this->controllerUrl;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function getFrontName()
    {
        return $this->frontName;
    }

    public function getClassName()
    {
        return $this->className;
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