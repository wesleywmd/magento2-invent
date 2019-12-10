<?php
namespace Wesleywmd\Invent\Model\Block;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\ModuleName;

class Data implements DataInterface
{
    private $blockName;

    private $className;

    private $directories;

    private $moduleName;

    public function __construct(ModuleName $moduleName, $blockName)
    {
        $this->blockName = $blockName;
        $this->moduleName = $moduleName;
        $this->directories = explode('/', $this->blockName);
        $this->className = ucfirst(array_pop($this->directories));
        $this->directories = array_merge(['Block'], $this->directories);
    }

    public function getBlockName()
    {
        return $this->blockName;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getNamespace()
    {
        return $this->moduleName->getNamespace($this->directories);
    }

    public function getPathPieces()
    {
        return array_merge($this->directories, [$this->className.'.php']);
    }
}