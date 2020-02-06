<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\BaseDiXml;
use Wesleywmd\Invent\Model\XmlParser\Dom;

class DiXml extends BaseDiXml implements RendererInterface
{
    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var \Wesleywmd\Invent\Model\Crud\Data $data */
        $this->addType($dom, 'Magento\Framework\View\Element\UiComponent\DataProvider\CollectionFactory', [
            'collections' => [
                $data->getListingDataSourceSlug() => $data->getModelGridCollectionInstance()
            ]
        ]);
        $magentoSearchResultInstance = 'Magento\Framework\View\Element\UiComponent\DataProvider\SearchResult';
        $this->addVirtualType($dom, $data->getModelGridCollectionInstance(), $magentoSearchResultInstance, [
            'mainTable' => $data->getTableName(),
            'resourceModel' => $data->getResourceModelInstance()
        ]);
    }
}