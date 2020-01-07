<?php
namespace Wesleywmd\Invent\Model\Model\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class SearchResultsInterfacePhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getSearchResultsInterfacePath();
    }
    
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