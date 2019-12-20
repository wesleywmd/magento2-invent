<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class PhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
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
        if (!$data->getNoEntityId()) {
            $class->addStmt($this->phpBuilder->methodModelGetter('entity_id', $data->getInterfaceName()))
                ->addStmt($this->phpBuilder->methodModelSetter('entity_id', $data->getInterfaceName()));
        }
        foreach ($data->getColumns() as $column) {
            $class->addStmt($this->phpBuilder->methodModelGetter($column, $data->getInterfaceName()))
                ->addStmt($this->phpBuilder->methodModelSetter($column, $data->getInterfaceName()));
        }
        if (!$data->getNoCreatedAt()) {
            $class->addStmt($this->phpBuilder->methodModelGetter('created_at', $data->getInterfaceName()))
                ->addStmt($this->phpBuilder->methodModelSetter('created_at', $data->getInterfaceName()));
        }
        if (!$data->getNoUpdatedAt()) {
            $class->addStmt($this->phpBuilder->methodModelGetter('updated_at', $data->getInterfaceName()))
                ->addStmt($this->phpBuilder->methodModelSetter('updated_at', $data->getInterfaceName()));
        }
        return $class;
    }
}