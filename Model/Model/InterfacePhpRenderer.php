<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class InterfacePhpRenderer implements PhpRendererInterface
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
        return $this->phpBuilder->namespace($data->getModuleName()->getNamespace(['Api', 'Data']))
            ->addStmt($this->getInterfaceStatement($data))
            ->getNode();
    }

    private function getInterfaceStatement(Data $data)
    {
        $interface = $this->phpBuilder->interface($data->getModelName().'Interface')
            ->addStmt($this->phpBuilder->const('DB_MAIN_TABLE', $data->getTableName()));
        if (!$data->getNoEntityId()) {
            $interface->addStmt($this->phpBuilder->const('ENTITY_ID', 'entity_id'))
                ->addStmt($this->phpBuilder->methodGetter('entity_id'))
                ->addStmt($this->phpBuilder->methodSetter('entity_id'));
        }
        foreach ($data->getColumns() as $column) {
            $interface->addStmt($this->phpBuilder->const(strtoupper($column), $column))
                ->addStmt($this->phpBuilder->methodGetter($column))
                ->addStmt($this->phpBuilder->methodSetter($column));
        }
        if (!$data->getNoCreatedAt()) {
            $interface->addStmt($this->phpBuilder->const('CREATED_AT', 'created_at'))
                ->addStmt($this->phpBuilder->methodGetter('created_at'))
                ->addStmt($this->phpBuilder->methodSetter('created_at'));
        }
        if (!$data->getNoUpdatedAt()) {
            $interface->addStmt($this->phpBuilder->const('UPDATED_AT', 'updated_at'))
                ->addStmt($this->phpBuilder->methodGetter('updated_at'))
                ->addStmt($this->phpBuilder->methodSetter('updated_at'));
        }
        return $interface;
    }
}