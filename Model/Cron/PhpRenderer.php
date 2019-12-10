<?php
namespace Wesleywmd\Invent\Model\Cron;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class PhpRenderer implements PhpRendererInterface
{
    private $phpBuilder;

    private $prettyPrinter;

    public function __construct(PhpBuilder $phpBuilder, PrettyPrinter $prettyPrinter)
    {
        $this->phpBuilder = $phpBuilder;
        $this->prettyPrinter = $prettyPrinter;
    }

    public function getContents(DataInterface $data)
    {
        return $this->prettyPrinter->print([$this->getBuilderNode($data)]);
    }

    private function getBuilderNode(Data $data)
    {
        return $this->phpBuilder->namespace($data->getNamespace())
            ->addStmt($this->phpBuilder->use('Psr\\Log\\LoggerInterface'))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        return $this->phpBuilder->class($data->getClassName())
            ->setDocComment('/**
                              * TODO implement '.$data->getCronName().' class body
                              */')
            ->addStmt($this->phpBuilder->property('logger')->makeProtected())
            ->addStmt($this->phpBuilder->constructor(['logger'=>'LoggerInterface']))
            ->addStmt($this->getExecuteMethod($data));
    }

    private function getExecuteMethod(Data $data)
    {
        $thisVar = $this->phpBuilder->var('this');
        $loggerFetch = $this->phpBuilder->propertyFetch($thisVar, 'logger');
        $loggerArgs = [$this->phpBuilder->nodeArg($this->phpBuilder->magicConstant(PhpBuilder::MAGIC_CONST_METHOD))];
        return $this->phpBuilder->method($data->getMethod())
            ->makePublic()
            ->setDocComment('/**
                              * TODO implement '.$data->getMethod().' method
                              */')
            ->addStmts([
                $this->phpBuilder->methodCall($loggerFetch, 'info', $loggerArgs),
                $this->phpBuilder->returnStmt($thisVar)
            ]);
    }
}