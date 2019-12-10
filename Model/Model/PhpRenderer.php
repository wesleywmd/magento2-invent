<?php
namespace Wesleywmd\Invent\Model\Model;

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
        return $this->phpBuilder->namespace($data->getModuleName()->getNamespace(['Model']))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Model\AbstractModel'))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Api','Data',$data->getModelName().'Interface'])))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        $tableName = $data->getModuleName()->getSlug([$data->getModelName()]);
        $class = $this->phpBuilder->class($data->getModelName())
            ->extend('AbstractModel')
            ->implement($data->getModelName().'Interface');
        if (!$data->getNoEntityId()) {
            $class->addStmt($this->phpBuilder->methodGetter('entity_id'))
                ->addStmt($this->phpBuilder->methodSetter('entity_id'));
        }
        foreach ($data->getColumns() as $column) {
            $class->addStmt($this->phpBuilder->methodGetter($column))
                ->addStmt($this->phpBuilder->methodSetter($column));
        }
        if (!$data->getNoCreatedAt()) {
            $class->addStmt($this->phpBuilder->methodGetter('created_at'))
                ->addStmt($this->phpBuilder->methodSetter('created_at'));
        }
        if (!$data->getNoUpdatedAt()) {
            $class->addStmt($this->phpBuilder->methodGetter('updated_at'))
                ->addStmt($this->phpBuilder->methodSetter('updated_at'));
        }
        return $class;
    }
}