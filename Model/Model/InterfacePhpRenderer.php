<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class InterfacePhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getInterfacePath();
    }
    
    protected function getNamespace(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getModuleName()->getNamespace(['Api', 'Data']);
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        $interface = $this->phpBuilder->interface($data->getInterfaceName())
            ->addStmt($this->phpBuilder->const('DB_MAIN_TABLE', $data->getTable()));
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