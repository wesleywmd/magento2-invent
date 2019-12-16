<?php
namespace Wesleywmd\Invent\Model\Command;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\ModuleName;

class Data implements DataInterface
{
    private $moduleName;

    private $commandName;

    private $directories;

    private $className;

    public function __construct(ModuleName $moduleName, $commandName)
    {
        $this->moduleName = $moduleName;
        $this->commandName = $commandName;
        $this->directories = ['Console', 'Command'];
        $this->className = implode('', array_map('ucfirst', explode(':', $this->commandName))) . 'Command';
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getCommandName()
    {
        return $this->commandName;
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getNamespace()
    {
        return $this->moduleName->getNamespace($this->directories);
    }

    public function getItemName()
    {
        return $this->moduleName->getSlug(explode(':', $this->commandName));
    }

    public function getInstance()
    {
        return $this->moduleName->getNamespace(array_merge($this->directories,[$this->className]));
    }

    public function getPath()
    {
        return $this->moduleName->getPath(array_merge($this->directories, [$this->className.'.php']));
    }
}