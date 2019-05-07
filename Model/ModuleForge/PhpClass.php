<?php
namespace Wesleywmd\Invent\Model\ModuleForge;

use Wesleywmd\Invent\Api\Data\PhpClassInterface;

class PhpClass implements \Wesleywmd\Invent\Api\Data\PhpClassInterface
{
    protected $fileName;

    protected $directories;

    protected $moduleName;

    protected $namespace;

    protected $useStatements = [];

    protected $className;

    protected $extends = "";

    protected $implements = "";

    protected $fields = [];

    protected $methods = [];

    public function __construct($moduleName)
    {
        $this->moduleName = $moduleName;
    }

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

    public function getInstance($prefix = false)
    {
        return $this->getNamespace($prefix) . "\\" . $this->getClassName();
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


    public function getImplements()
    {
        return $this->implements;
    }

    public function setImplements($implements)
    {
        $this->implements = $implements;
        return $this;
    }

    public function getFields()
    {
        return $this->fields;
    }

    public function addField($name, $privilege = \Wesleywmd\Invent\Api\Data\PhpClassInterface::PRIV_PRIVATE, $value = null)
    {
        $this->fields[$name]["priv"] = $privilege;
        if( ! is_null($value) ) {
            $this->fields[$name]["value"] = $value;
        }
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

    public function addSetterMethod($interface, $attribute)
    {
        $camelAttribute = implode("", array_map("ucfirst", explode("_", $attribute)));
        $this->addMethod("set".$camelAttribute, PhpClassInterface::PRIV_PUBLIC, [
            $attribute => []
        ], [
            "return \$this->setData($interface::".strtoupper($attribute).", \${$attribute});"
        ]);
        return $this;
    }

    public function addGetterMethod($interface, $attribute)
    {
        $camelAttribute = implode("", array_map("ucfirst", explode("_", $attribute)));
        $this->addMethod("get".$camelAttribute, PhpClassInterface::PRIV_PUBLIC, [], [
            "return \$this->_getData($interface::".strtoupper($attribute).");"
        ]);
        return $this;
    }

    public function hasMethods()
    {
        return !empty($this->methods);
    }
}