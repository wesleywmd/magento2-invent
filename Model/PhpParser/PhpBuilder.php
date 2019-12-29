<?php
namespace Wesleywmd\Invent\Model\PhpParser;

use PhpParser\Builder;
use PhpParser\BuilderFactory;

class PhpBuilder extends BuilderFactory
{
    const MAGIC_CONST_CLASS = 'class';
    const MAGIC_CONST_DIR = 'dir';
    const MAGIC_CONST_FILE = 'file';
    const MAGIC_CONST_FUNCTION = 'function';
    const MAGIC_CONST_LINE = 'line';
    const MAGIC_CONST_METHOD = 'method';
    const MAGIC_CONST_NAMESPACE = 'namespace';
    const MAGIC_CONST_TRAIT = 'trait';

    public function assign($var, $expr, $attributes = [])
    {
        return new \PhpParser\Node\Expr\Assign($var, $expr, $attributes = []);
    }

    public function returnStmt(\PhpParser\Node\Expr $expr = null, array $attributes = [])
    {
        return new \PhpParser\Node\Stmt\Return_($expr, $attributes);
    }

    public function nodeArg($value, $byRef = false, $unpack = false, $attributes = [])
    {
        return new \PhpParser\Node\Arg($value, $byRef, $unpack, $attributes);
    }

    public function magicConstant($type, $attributes = [])
    {
        switch ($type) {
            case self::MAGIC_CONST_CLASS:
                return new \PhpParser\Node\Scalar\MagicConst\Class_($attributes);
            case self::MAGIC_CONST_DIR:
                return new \PhpParser\Node\Scalar\MagicConst\Dir($attributes);
            case self::MAGIC_CONST_FILE:
                return new \PhpParser\Node\Scalar\MagicConst\File($attributes);
            case self::MAGIC_CONST_FUNCTION:
                return new \PhpParser\Node\Scalar\MagicConst\Function_($attributes);
            case self::MAGIC_CONST_LINE:
                return new \PhpParser\Node\Scalar\MagicConst\Line($attributes);
            case self::MAGIC_CONST_METHOD:
                return new \PhpParser\Node\Scalar\MagicConst\Method($attributes);
            case self::MAGIC_CONST_NAMESPACE:
                return new \PhpParser\Node\Scalar\MagicConst\Namespace_($attributes);
            case self::MAGIC_CONST_TRAIT:
                return new \PhpParser\Node\Scalar\MagicConst\Trait_($attributes);
            default:
                throw new \Exception('Magic Constant Type [' . $type . '] does not exist');
        }
    }

    public function constructor($parameters = [], $parent = false, $parentArgs = [])
    {
        $constructor = $this->method('__construct')->makePublic();
        if ($parent) {
            $args = [];
            foreach ($parentArgs as $arg) {
                $args[] = $this->nodeArg($this->var($arg));
            }
            $constructor->addStmt($this->staticCall('parent', '__construct', $args));
        }
        foreach ($parameters as $parameter => $type) {
            $param = $this->param($parameter)->setType($type);
            $constructor->addParam($param);

            if (in_array($parameter, $parentArgs)) {
                continue;
            }

            $constructor->addStmt($this->assign(
                $this->propertyFetch(
                    $this->var('this'), $param->getNode()->var->name
                ), $this->var($param->getNode()->var->name)
            ));
        }
        return $constructor;
    }

    public function const($name, $value, $flags = 0, $attributes = [], $stmtAttributes = [])
    {
        return new \PhpParser\Node\Stmt\ClassConst([
            new \PhpParser\Node\Const_($name, $this->val($value), $attributes)
        ], $flags, $stmtAttributes);
    }

    public function methodGetter($value)
    {
        $methodName = explode('_', $value);
        $methodName = array_map( function($piece) { return ucfirst($piece); }, $methodName);
        $methodName = implode('', $methodName);
        return $this->method('get'.$methodName)->makePublic()
            ->addStmt($this->returnStmt($this->propertyFetch($this->var('this'),lcfirst($methodName))));
    }

    public function methodModelGetter($value, $interface)
    {
        $methodName = explode('_', $value);
        $constName = array_map( function($piece) { return strtoupper($piece); }, $methodName);
        $constName = implode('_', $constName);
        $methodName = array_map( function($piece) { return ucfirst($piece); }, $methodName);
        $methodName = implode('', $methodName);
        return $this->method('get'.$methodName)->makePublic()
            ->addStmt($this->returnStmt($this->methodCall($this->var('this'),'_getData', [
                $this->classConstFetch($interface, $constName)
            ])));
    }

    public function methodSetter($value)
    {
        $methodName = explode('_', $value);
        $methodName = array_map( function($piece) { return ucfirst($piece); }, $methodName);
        $methodName = implode('', $methodName);
        return $this->method('set'.$methodName)->makePublic()
            ->addParam($this->param(lcfirst($methodName)))
            ->addStmt($this->assign($this->propertyFetch($this->var('this'),lcfirst($methodName)), $this->var(lcfirst($methodName))))
            ->addStmt($this->returnStmt($this->var('this')));
    }

    public function methodModelSetter($value, $interface)
    {
        $methodName = explode('_', $value);
        $constName = array_map( function($piece) { return strtoupper($piece); }, $methodName);
        $constName = implode('_', $constName);
        $methodName = array_map( function($piece) { return ucfirst($piece); }, $methodName);
        $methodName = implode('', $methodName);
        return $this->method('set'.$methodName)->makePublic()
            ->addParam($this->param(lcfirst($methodName)))
            ->addStmt($this->methodCall($this->var('this'),'setData', [
                $this->classConstFetch($interface, $constName),
                $this->var(lcfirst($methodName))
            ]))
            ->addStmt($this->returnStmt($this->var('this')));
    }

    public function name($name)
    {
        return new \PhpParser\Node\Name($name);
    }

    public function throwNew($name, $args = [], $attributes = [], $throwAttributes = [])
    {
        $new_ = new \PhpParser\Node\Expr\New_($this->name($name), $args, $attributes);
        return new \PhpParser\Node\Stmt\Throw_($new_, $throwAttributes);
    }

    public function catch($type, $var, $stmts = [], $attributes = [])
    {
        return new \PhpParser\Node\Stmt\Catch_([$this->name($type)], $this->var($var), $stmts, $attributes);
    }

    public function tryCatch($stmts, $catches, \PhpParser\Node\Stmt\Finally_ $finally = null, $attributes = [])
    {
        return new \PhpParser\Node\Stmt\TryCatch($stmts, $catches, $finally, $attributes);
    }

    public function booleanNot(\PhpParser\Node\Expr $expr, $attributes = [])
    {
        return new \PhpParser\Node\Expr\BooleanNot($expr, $attributes);
    }

    public function if(\PhpParser\Node\Expr $cond, $subNodes = [], $attributes = [])
    {
        return new \PhpParser\Node\Stmt\If_($cond, $subNodes, $attributes);
    }

    public function thisPropertyFetch($name)
    {
        return $this->propertyFetch($this->var('this'), $name);
    }

    public function thisMethodCall($name, $args = [])
    {
        return $this->methodCall($this->var('this'), $name, $args);
    }
}