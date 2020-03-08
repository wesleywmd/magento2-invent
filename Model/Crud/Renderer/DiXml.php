<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\BaseDiXml;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\XmlParser\Dom;

class DiXml extends BaseDiXml implements RendererInterface
{
    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Crud\Data $data */
        $this->addType($dom, 'Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory', [
            'collections' => [
                $data->getUiListingDataSource() => $data->getModelGridCollectionInstance()
            ]
        ]);
        $this->addVirtualType($dom,
            $data->getModelGridCollectionInstance(),
            'Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult',
            ['mainTable' => $data->getModel()->getTable(), 'resourceModel' => $data->getModel()->getResourceModelInstance()]
        );
    }
}