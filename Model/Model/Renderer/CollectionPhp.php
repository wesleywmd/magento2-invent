<?php
namespace Wesleywmd\Invent\Model\Model\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class CollectionPhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getCollectionPath();
    }

    protected function getNamespace(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getModuleName()->getNamespace(['Model','ResourceModel',$data->getModelName()]);
    }

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Data $data */
        return [
            'Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection',
            $data->getInterfaceInstance(),
            $data->getInstance(),
            $data->getResourceModelName() => $data->getResourceModelInstance()
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        $class = $this->phpBuilder->class('Collection')
            ->extend('AbstractCollection')
            ->addStmt($this->phpBuilder->property('_idFieldName')->makeProtected()
                ->setDefault($this->phpBuilder->classConstFetch($data->getResourceModelName(), 'ENTITY_ID'))
            )
            ->addStmt($this->phpBuilder->property('_eventPrefix')->makeProtected()
                ->setDefault($data->getModuleName()->getSlug([$data->getModelName(), 'collection']))
            )
            ->addStmt($this->phpBuilder->property('_eventObject')->makeProtected()
                ->setDefault($data->getVar().'_collection')
            )
            ->addStmt($this->phpBuilder->method('_construct')->makeProtected()
                ->addStmt($this->phpBuilder->methodCall($this->phpBuilder->var('this'), '_init', [
                    $this->phpBuilder->classConstFetch($data->getModelName(), 'class'),
                    $this->phpBuilder->classConstFetch($data->getResourceModelName(), 'class')
                ]))
            );
        return $class;
    }
}