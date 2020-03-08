<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class RoutesXml extends AbstractXmlRenderer implements RendererInterface
{
    protected function getType()
    {
        return Location::TYPE_ROUTE;
    }

    protected function getArea(DataInterface $data)
    {
        return Location::AREA_ADMINHTML;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $routerXpath = ['router[@id="admin"]'];
        $routeXpath = array_merge($routerXpath, ['route[@id="'.$data->getRouterFrontName().'"]']);
        $moduleXpath = array_merge($routeXpath, ['module[@name="'.$data->getModuleName()->getName().'"]']);
        $dom->updateElement('router', 'id', 'admin')
            ->updateElement('route', 'id', $data->getRouterFrontName(), null, $routerXpath)
            ->updateAttribute('frontName', $data->getRouterFrontName(), $routeXpath)
            ->updateElement('module', 'name', $data->getModuleName()->getName(), null, $routeXpath)
            ->updateAttribute('before', 'Magento_Backend', $moduleXpath);
    }
}