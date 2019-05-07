<?php
namespace Wesleywmd\Invent\Api\Data;

interface PhpClassInterface
{
    const PRIV_PRIVATE = "private";
    const PRIV_PROTECTED = "protected";
    const PRIV_PUBLIC = "public";
    const PRIV_CONST = "const";

    public function getFileName();
    public function getDirectories();
    public function setDirectories($directories);
    public function getModule();
    public function setModule($moduleName);
    public function getNamespace();
    public function getUseStatements();
    public function addUseStatement($useStatement);
    public function getClassName();
    public function setClassName($className);
    public function getInstance($prefix = false);
    public function getExtends();
    public function setExtends($extends);
    public function getImplements();
    public function setImplements($implements);
    public function getFields();
    public function addField($name, $privilege = PhpClassInterface::PRIV_PRIVATE);
    public function hasFields();
    public function getMethods();
    public function addMethod($name, $privilege = PhpClassInterface::PRIV_PRIVATE, $params = [], $contents = []);
    public function hasMethods();
}