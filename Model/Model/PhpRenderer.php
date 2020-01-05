<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class PhpRenderer extends AbstractPhpRenderer implements RendererInterface
{
    protected function getUseStatements(DataInterface $data)
    {
        /** @var Data $data */
        return [
            'Magento\Framework\Model\AbstractModel',
            $data->getInterfaceInstance()
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        $class = $this->phpBuilder->class($data->getClassName())
            ->extend('AbstractModel')
            ->implement($data->getInterfaceName());
        foreach ($this->getColumns($data) as $column) {
            $class->addStmt($this->phpBuilder->methodModelGetter($column, $data->getInterfaceName()))
                ->addStmt($this->phpBuilder->methodModelSetter($column, $data->getInterfaceName()));
        }
        return $class;
    }

    private function getColumns(DataInterface $data)
    {
        /** @var Data $data */
        $columns = $data->getColumns();
        if (!$data->getNoEntityId()) {
            array_unshift($columns, 'entity_id');
        }
        if (!$data->getNoCreatedAt()) {
            array_push($columns, 'created_at');
        }
        if (!$data->getNoUpdatedAt()) {
            array_push($columns, 'updated_at');
        }
        return $columns;
    }
}