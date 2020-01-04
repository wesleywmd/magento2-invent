<?php
namespace Wesleywmd\Invent\Model\Menu;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements XmlRendererInterface
{
    protected function getType()
    {
        return Location::TYPE_MENU;
    }
    
    protected function getArea(DataInterface $data)
    {
        return Location::AREA_ADMINHTML;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $dom->updateElement('menu')
            ->updateElement('add', 'id', $data->getMenuResource(), null, ['menu'])
            ->updateAttributes([
                'title' => $data->getTitle(),
                'module' => $data->getModuleName()->getName(),
                'sortOrder' => $data->getSortOrder(),
                'resource' => $data->getResource()
            ], ['menu', 'add[@id="'.$data->getMenuResource().'"]']);
        if (!is_null($data->getParentMenu())) {
            $dom->updateAttributes([
                'parent' => $data->getParentMenu(),
                'action' => $data->getAction()
            ], ['menu', 'add[@id="'.$data->getMenuResource().'"]']);
        }
    }
}