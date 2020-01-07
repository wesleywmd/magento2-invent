<?php
namespace Wesleywmd\Invent\Model\Controller\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class RouteXml extends AbstractXmlRenderer implements RendererInterface
{
    protected function getType()
    {
        return Location::TYPE_ROUTE;
    }

    protected function getArea(DataInterface $data)
    {
        return Location::AREA_FRONTEND;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $routerNodeXpath = $this->addKeyedNode($dom, 'router', 'id', $data->getRouter());
        $routeNodeXpath = $this->addKeyedNode($dom, 'route', 'id', $data->getFrontName(), $routerNodeXpath);
        $this->addKeyedNode($dom, 'module', 'name', $data->getModuleName()->getName(), $routeNodeXpath);
    }
}