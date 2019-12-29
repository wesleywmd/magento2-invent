<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class SearchResultsInterfacePhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getNamespace(DataInterface $data)
    {
        return $data->getModuleName()->getNamespace(['Api','Data']);
    }

    protected function getUseStatements(DataInterface $data)
    {
        return ['Magento\Framework\Api\SearchResultsInterface'];
    }

    protected function getClassStatement(DataInterface $data)
    {
        return $this->phpBuilder->interface($data->getModelName().'SearchResultsInterface')
            ->extend('SearchResultsInterface')
            ->addStmt($this->phpBuilder->method('getItems')->makePublic())
            ->addStmt($this->phpBuilder->method('setItems')->makePublic()
                ->addParam($this->phpBuilder->param('items')->setType('array'))
            );
    }
}