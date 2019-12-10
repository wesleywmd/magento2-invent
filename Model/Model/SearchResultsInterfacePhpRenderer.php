<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class SearchResultsInterfacePhpRenderer implements PhpRendererInterface
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
        return $this->phpBuilder->namespace($data->getModuleName()->getNamespace(['Api','Data']))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\SearchResultsInterface'))
            ->addStmt($this->getInterfaceStatement($data))
            ->getNode();
    }

    private function getInterfaceStatement(Data $data)
    {
        return $this->phpBuilder->interface($data->getModelName().'SearchResultsInterface')
            ->extend('SearchResultsInterface')
            ->addStmt($this->phpBuilder->method('getItems')->makePublic())
            ->addStmt($this->phpBuilder->method('setItems')->makePublic()->addParam($this->phpBuilder->param('items')->setType('array')));
    }
}