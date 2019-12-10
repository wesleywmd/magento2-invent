<?php
namespace Wesleywmd\Invent\Model\Block;

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
            ->addStmt($this->phpBuilder->use('Magento\\Framework\\View\\Element\\Template'))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        return $this->phpBuilder->class($data->getClassName())
            ->extend('Template')
            ->setDocComment('/**
                              * TODO implement '.$data->getBlockName().' class body
                              */');
    }
}