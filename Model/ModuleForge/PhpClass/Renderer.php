<?php
namespace Wesleywmd\Invent\Model\ModuleForge\PhpClass;

use Wesleywmd\Invent\Api\Data\PhpClassInterface;

class Renderer
{
    public function phpClassToString(\Wesleywmd\Invent\Api\Data\PhpClassInterface $phpClass)
    {
        $classString =  "<?php\n";
        $classString .= "namespace {$phpClass->getNamespace()};\n";
        $classString .= "\n";
        $classString .= $this->renderUseStatements($phpClass->getUseStatements());
        $classString .= "class {$phpClass->getClassName()} {$this->renderExtends($phpClass->getExtends())} {$this->renderImplements($phpClass->getImplements())}\n";
        $classString .= "{\n";
        if( !$phpClass->hasFields() && !$phpClass->hasMethods() ) {
            $classString .= "    // @TODO implement {$phpClass->getNamespace()}\\{$phpClass->getClassName()}\n";
        } else {
            $classString .= $this->renderFields($phpClass->getFields());
            $classString .= ( $phpClass->hasFields() && $phpClass->hasMethods() ) ? "\n" : "";
            $classString .= $this->renderMethods($phpClass->getMethods());
        }
        $classString .= "}";
        return $classString;
    }

    public function interfaceToString(\Wesleywmd\Invent\Api\Data\PhpClassInterface $phpInterface)
    {
        $interfaceString =  "<?php\n";
        $interfaceString .= "namespace {$phpInterface->getNamespace()};\n";
        $interfaceString .= "\n";
        $interfaceString .= $this->renderUseStatements($phpInterface->getUseStatements());
        $interfaceString .= "interface {$phpInterface->getClassName()} {$this->renderExtends($phpInterface->getExtends())}\n";
        $interfaceString .= "{\n";
        if( !$phpInterface->hasFields() && !$phpInterface->hasMethods() ) {
            $interfaceString .= "    // @TODO implement {$phpInterface->getNamespace()}\\{$phpInterface->getClassName()}\n";
        } else {
            $interfaceString .= $this->renderFields($phpInterface->getFields());
            $interfaceString .= ( $phpInterface->hasFields() && $phpInterface->hasMethods() ) ? "\n" : "";
            $interfaceString .= $this->renderInterfaceMethods($phpInterface->getMethods());
        }
        $interfaceString .= "}";
        return $interfaceString;
    }

    private function renderUseStatements($useStatements)
    {
        $useStatementsString = "";
        foreach( $useStatements as $use ) {
            $useStatementsString .= "use $use;\n";
        }
        return $useStatementsString . ((!empty($useStatements))?"\n":"");
    }

    private function renderExtends($extends)
    {
        return ($extends!="") ? "extends ".$extends : "";
    }

    private function renderImplements($implements)
    {
        return ($implements!="") ? "implements ".$implements : "";
    }

    private function renderParams($params)
    {
        $paramString = "";
        foreach( $params as $param => $paramBag ) {
            if( strlen($paramString) > 0 ) {
                $paramString .= ", ";
            }
            if( isset($paramBag["type"]) ) {
                $paramString .= "{$paramBag["type"]} \${$param}";
            } else {
                $paramString .= "\${$param}";
            }
        }
        return $paramString;
    }

    private function renderFields($fields)
    {
        $fieldsString = "";
        foreach( $fields as $field => $fieldBag ) {
            if( $fieldBag["priv"] === PhpClassInterface::PRIV_CONST) {
                $fieldsString .= "    const ". strtoupper($field);
            } else {
                $fieldsString .= "    {$fieldBag["priv"]} \${$field}";
            }
            if( isset($fieldBag["value"]) ) {
                $fieldsString .= " = \"{$fieldBag["value"]}\";\n";
            } else {
                $fieldsString .= ";\n";
            }
        }
        return $fieldsString;
    }

    private function renderMethods($methods)
    {
        $methodsString = "";
        $count = 1;
        foreach( $methods as $method => $methodBag ) {
            $methodsString .= ( $count>1 ) ? "\n" : "";
            $methodsString .= "    {$methodBag["priv"]} function {$method}(";
            $methodsString .= $this->renderParams($methodBag["params"]) . ")\n    {\n";
            foreach( $methodBag["contents"] as $line ) {
                $methodsString .= "        {$line}\n";
            }
            $methodsString .= "    }\n";
            $count++;
        }
        return $methodsString;
    }

    private function renderInterfaceMethods($methods)
    {
        {
            $methodsString = "";
            $count = 1;
            foreach( $methods as $method => $methodBag ) {
                $methodsString .= ( $count>1 ) ? "\n" : "";
                $methodsString .= "    public function {$method}(";
                $methodsString .= $this->renderParams($methodBag["params"]) . ");\n";
                $count++;
            }
            return $methodsString;
        }
    }
}