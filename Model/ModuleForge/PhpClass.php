<?php
namespace Wesleywmd\Invent\Model\ModuleForge;

class PhpClass implements \Wesleywmd\Invent\Api\Data\PhpClassInterface
{
    protected $fileName;

    protected $directories;

    protected $moduleName;

    protected $namespace;

    protected $useStatements = [];

    protected $className;

    protected $extends = "";

    protected $fields = [];

    protected $methods = [];

    public function getFileName()
    {
        return $this->getClassName() . ".php";
    }

    public function getDirectories()
    {
        return $this->directories;
    }

    public function setDirectories($directories)
    {
        $this->directories = $directories;
        return $this;
    }

    public function getModule()
    {
        return $this->moduleName;
    }

    public function setModule($moduleName)
    {
        $this->moduleName = $moduleName;
        return $this;
    }

    public function getNamespace($prefix = false)
    {
        $namespace = str_replace("_", "\\", $this->moduleName);
        foreach( $this->directories as $dir ) {
            $namespace .= "\\" . ucfirst($dir);
        }
        $prefix = ($prefix) ? "\\" : "";
        return $prefix . $namespace;
    }

    public function getInstance()
    {
        return $this->getNamespace() . "\\" . $this->getClassName();
    }

    public function getUseStatements()
    {
        return $this->useStatements;
    }

    public function addUseStatement($useStatement)
    {
        $this->useStatements[] = $useStatement;
        return $this;
    }

    public function getClassName()
    {
        return $this->className;
    }

    public function setClassName($className)
    {
        $this->className = $className;
        return $this;
    }

    public function getExtends()
    {
        return $this->extends;
    }

    public function setExtends($extends)
    {
        $this->extends = $extends;
        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function addField($name, $privilege = \Wesleywmd\Invent\Api\Data\PhpClassInterface::PRIV_PRIVATE)
    {
        $this->fields[$name] = ["priv"=>$privilege];
        return $this;
    }

    public function hasFields()
    {
        return !empty($this->fields);
    }

    public function getMethods()
    {
        return $this->methods;
    }

    public function addMethod($name, $privilege = \Wesleywmd\Invent\Api\Data\PhpClassInterface::PRIV_PRIVATE, $params = [], $contents = [])
    {
        $this->methods[$name] = ["priv" => $privilege, "params" => $params, "contents" => $contents];
        return $this;
    }

    public function hasMethods()
    {
        return !empty($this->methods);
    }
}