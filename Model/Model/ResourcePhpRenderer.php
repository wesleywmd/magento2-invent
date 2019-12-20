<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class ResourcePhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getNamespace(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getModuleName()->getNamespace(['Model','ResourceModel']);
    }

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Data $data */
        return [
            'Magento\Framework\Model\ResourceModel\Db\AbstractDb',
            $data->getInterfaceInstance()
        ];
    }

    protected function getClassStatement(Data $data)
    {
        $class = $this->phpBuilder->class($data->getClassName())
            ->extend('AbstractDb')
            ->addStmt($this->phpBuilder->method('_construct')->makeProtected()
                ->addStmt($this->phpBuilder->methodCall($this->phpBuilder->var('this'), '_init', [
                    $this->phpBuilder->classConstFetch($data->getInterfaceName(), 'DB_MAIN_TABLE'),
                    $this->phpBuilder->classConstFetch($data->getInterfaceName(), 'ENTITY_ID')
                ]))
            );
        return $class;
    }
}