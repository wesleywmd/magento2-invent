<?php
namespace Wesleywmd\Invent\Model\Controller;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements XmlRendererInterface
{
    protected function getType()
    {
        return Location::TYPE_ROUTE;
    }

    protected function getArea()
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