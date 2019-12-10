<?php
namespace Wesleywmd\Invent\Model\Command;

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
            ->addStmt($this->phpBuilder->use('Symfony\\Component\\Console\\Command\\Command'))
            ->addStmt($this->phpBuilder->use('Symfony\\Component\\Console\\Input\\InputInterface'))
            ->addStmt($this->phpBuilder->use('Symfony\\Component\\Console\\Output\\OutputInterface'))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        return $this->phpBuilder->class($data->getClassName())
            ->extend('Command')
            ->addStmt($this->getConfigureMethod($data))
            ->addStmt($this->getExecuteMethod($data));
    }

    private function getConfigureMethod(Data $data)
    {
        $varThis = $this->phpBuilder->var('this');
        $setNameArgs = [$this->phpBuilder->nodeArg($this->phpBuilder->val($data->getCommandName()))];
        return $this->phpBuilder->method('configure')
            ->makeProtected()
            ->setDocComment('/**
                              * TODO implement configure method
                              */')
            ->addStmt($this->phpBuilder->methodCall($varThis, 'setName', $setNameArgs));
    }

    private function getExecuteMethod(Data $data)
    {
        return $this->phpBuilder->method('execute')
            ->makeProtected()
            ->setDocComment('/**
                              * TODO implement execute method
                              */')
            ->addParam($this->phpBuilder->param('input')->setType('InputInterface'))
            ->addParam($this->phpBuilder->param('output')->setType('OutputInterface'));
    }
}