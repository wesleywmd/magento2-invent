<?php
namespace Wesleywmd\Invent\Model\ModuleForge\PhpClass;

class Renderer
{
    public function PhpClassToString(\Wesleywmd\Invent\Api\Data\PhpClassInterface $phpClass)
    {
        $classString =  "<?php\n";
        $classString .= "namespace {$phpClass->getNamespace()};\n";
        $classString .= "\n";
        $classString .= $this->renderUseStatements($phpClass->getUseStatements());
        $classString .= "class {$phpClass->getClassName()} {$this->renderExtends($phpClass->getExtends())}\n";
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

    private function renderParams($params)
    {
        $paramString = "";
        foreach( $params as $param => $paramBag ) {
            if( strlen($paramString) > 0 ) {
                $paramString .= ",\n";
            }
            $paramString .= "        {$paramBag["type"]} \${$param}";
        }
        return $paramString;
    }

    private function renderFields($fields)
    {
        $fieldsString = "";
        foreach( $fields as $field => $fieldBag ) {
            $fieldsString .= "    {$fieldBag["priv"]} \${$field};\n";
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
            if( empty($methodBag["params"]) ) {
                $methodsString .= ")\n    {\n";
            } else {
                $methodsString .= "\n" . $this->renderParams($methodBag["params"]) . "\n    ) {\n";
            }
            foreach( $methodBag["contents"] as $line ) {
                $methodsString .= "        {$line}\n";
            }
            $methodsString .= "    }\n";
            $count++;
        }
        return $methodsString;
    }
}