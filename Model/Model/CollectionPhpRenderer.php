<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class CollectionPhpRenderer implements PhpRendererInterface
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
        return $this->phpBuilder->namespace($data->getModuleName()->getNamespace(['Model','ResourceModel',$data->getModelName()]))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection'))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Api','Data',$data->getModelName().'Interface'])))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Model',$data->getModelName()])))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Model','resourceModel',$data->getModelName()]))->as($data->getModelName().'Resource'))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        $tableName = $data->getModuleName()->getSlug([$data->getModelName()]);
        $class = $this->phpBuilder->class('Collection')
            ->extend('AbstractCollection')
            ->addStmt($this->phpBuilder->property('_idFieldName')->makeProtected()->setDefault($this->phpBuilder->classConstFetch($data->getModelName().'Interface','ENTITY_ID')))
            ->addStmt($this->phpBuilder->property('_eventPrefix')->makeProtected()->setDefault($data->getModuleName()->getSlug([$data->getModelName(),'collection'])))
            ->addStmt($this->phpBuilder->property('_eventObject')->makeProtected()->setDefault(strtolower($data->getModelName()).'_collection'))
            ->addStmt($this->phpBuilder->method('_construct')
                ->makeProtected()
                ->addStmt($this->phpBuilder->methodCall($this->phpBuilder->var('this'), '_init', [
                    $this->phpBuilder->classConstFetch($data->getModelName(),'class'),
                    $this->phpBuilder->classConstFetch($data->getModelName().'Resource','class')
                ]))
            );
        return $class;
    }
}