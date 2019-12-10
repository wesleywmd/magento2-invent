<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class RepositoryInterfacePhpRenderer implements PhpRendererInterface
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
        return $this->phpBuilder->namespace($data->getModuleName()->getNamespace(['Api']))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\SearchCriteriaInterface'))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Api','Data',$data->getModelName().'Interface'])))
            ->addStmt($this->getInterfaceStatement($data))
            ->getNode();
    }

    private function getInterfaceStatement(Data $data)
    {
        $interfaceParam = $this->phpBuilder->param(strtolower($data->getModelName()))->setType($data->getModelName().'Interface');
        $idParam = $this->phpBuilder->param(strtolower($data->getModelName()).'Id');
        return $this->phpBuilder->interface($data->getModelName().'RepositoryInterface')
            ->addStmt($this->phpBuilder->method('save')->makePublic()->addParam($interfaceParam))
            ->addStmt($this->phpBuilder->method('getById')->makePublic()->addParam($idParam))
            ->addStmt($this->phpBuilder->method('getList')->makePublic()->addParam($this->phpBuilder->param('searchCriteriaInterface')->setType('searchCriteria')))
            ->addStmt($this->phpBuilder->method('delete')->makePublic()->addParam($interfaceParam))
            ->addStmt($this->phpBuilder->method('deleteById')->makePublic()->addParam($idParam));
    }
}