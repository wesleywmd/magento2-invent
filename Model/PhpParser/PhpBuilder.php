<?php
namespace Wesleywmd\Invent\Model\PhpParser;

use PhpParser\BuilderFactory;

class PhpBuilder extends BuilderFactory
{
    public function moduleNamespace($moduleName, $directories = [])
    {
        $namespace = str_replace('_', '\\', $moduleName);
        foreach( $directories as $dir ) {
            $namespace .= '\\' . ucfirst($dir);
        }
        return $this->namespace($namespace);
    }
    
    public function construct($params = [])
    {
        $stmts = [];
        foreach( $params as $param ) {
            $stmts[] = new \PhpParser\Node\Expr\Assign(
                new \PhpParser\Node\Expr\PropertyFetch(
                    $this->var('this'), $param->getNode()->var->name
                ), $this->var($param->getNode()->var->name)
            );
        }
        return $this->method('__construct')->makePublic()->addParams($params)->addStmts($stmts);
    }
}