<?php
namespace Wesleywmd\Invent\Model\PhpParser;

use PhpParser\BuilderFactory;
use PhpParser\Node;

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

    public function assign(Node\Expr $var, Node\Expr $expr, array $attributes = [])
    {
        return new Node\Expr\Assign($var, $expr, $attributes);
    }

    public function assignSimple($var, $val, array $attributes = [])
    {
        return $this->assign($this->var($var), $this->val($val), $attributes);
    }

    public function returnStmt(Node\Expr $expr = null, array $attributes = [])
    {
        return new Node\Stmt\Return_($expr, $attributes);
    }

    public function nodeArg($value, $byRef = false, $unpack = false, $attributes = [])
    {
        return new Node\Arg($value, $byRef, $unpack, $attributes);
    }

    public function magicConstant($type, $attributes = [])
    {
        switch ($type) {
            case self::MAGIC_CONST_CLASS:
                return new Node\Scalar\MagicConst\Class_($attributes);
            case self::MAGIC_CONST_DIR:
                return new Node\Scalar\MagicConst\Dir($attributes);
            case self::MAGIC_CONST_FILE:
                return new Node\Scalar\MagicConst\File($attributes);
            case self::MAGIC_CONST_FUNCTION:
                return new Node\Scalar\MagicConst\Function_($attributes);
            case self::MAGIC_CONST_LINE:
                return new Node\Scalar\MagicConst\Line($attributes);
            case self::MAGIC_CONST_METHOD:
                return new Node\Scalar\MagicConst\Method($attributes);
            case self::MAGIC_CONST_NAMESPACE:
                return new Node\Scalar\MagicConst\Namespace_($attributes);
            case self::MAGIC_CONST_TRAIT:
                return new Node\Scalar\MagicConst\Trait_($attributes);
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
        return new Node\Stmt\ClassConst([
            new Node\Const_($name, $this->val($value), $attributes)
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
        return new Node\Name($name);
    }

    public function throwNew($name, $args = [], $attributes = [], $throwAttributes = [])
    {
        $new_ = new Node\Expr\New_($this->name($name), $args, $attributes);
        return new Node\Stmt\Throw_($new_, $throwAttributes);
    }

    public function catch($type, $var, $stmts = [], $attributes = [])
    {
        return new Node\Stmt\Catch_([$this->name($type)], $this->var($var), $stmts, $attributes);
    }

    public function tryCatch($stmts, $catches, Node\Stmt\Finally_ $finally = null, $attributes = [])
    {
        return new Node\Stmt\TryCatch($stmts, $catches, $finally, $attributes);
    }

    public function booleanNot(Node\Expr $expr, $attributes = [])
    {
        return new Node\Expr\BooleanNot($expr, $attributes);
    }

    public function identical(Node\Expr $left, Node\Expr $right, array $attributes = [])
    {
        return new Node\Expr\BinaryOp\Identical($left, $right, $attributes);
    }

    public function if(Node\Expr $cond, $subNodes = [], $attributes = [])
    {
        return new Node\Stmt\If_($cond, $subNodes, $attributes);
    }

    public function elseif(Node\Expr $cond, $stmts = [], $attributes = [])
    {
        return new Node\Stmt\ElseIf_($cond, $stmts, $attributes);
    }

    public function else(array $stmts = [], array $attributes = [])
    {
        return new Node\Stmt\Else_($stmts, $attributes);
    }

    public function thisPropertyFetch($name)
    {
        return $this->propertyFetch($this->var('this'), $name);
    }

    public function thisMethodCall($name, $args = [])
    {
        return $this->methodCall($this->var('this'), $name, $args);
    }

    public function arrayDefine(array $items = [], array $attributes = [])
    {
        $attributes = array_merge(['kind' => Node\Expr\Array_::KIND_SHORT], $attributes);
        return new Node\Expr\Array_($items, $attributes);
    }

    public function arrayItem(Node\Expr $value, Node\Expr $key = null, bool $byRef = false, array $attributes = [], bool $unpack = false)
    {
        return new Node\Expr\ArrayItem($value, $key, $byRef, $attributes, $unpack);
    }

    public function arrayDimFetch(Node\Expr $var, Node\Expr $dim = null, $attributes = [])
    {
        return new Node\Expr\ArrayDimFetch($var, $dim, $attributes);
    }

    public function arrayMultiDimFetch(Node\Expr $var, $dims = [], $attributes = [])
    {
        foreach ($dims as $dim) {
            $var = $this->arrayDimFetch($var, $dim, $attributes);
        }
        return $var;
    }

    public function foreachLoop(Node\Expr $expr, Node\Expr $valueVar, array $subNodes = [], array $attributes = [])
    {
        return new Node\Stmt\Foreach_($expr, $valueVar, $subNodes, $attributes);
    }

    public function continue(Node\Expr $num = null, array $attributes = [])
    {
        return new Node\Stmt\Continue_($num, $attributes);
    }

    public function translate($phrase, $args = [])
    {
        if (is_string($phrase)) {
            $phrase = $this->val($phrase);
        }
        return $this->funcCall('__', array_merge([$phrase], $args));
    }
}