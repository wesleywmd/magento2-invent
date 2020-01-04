<?php
namespace Wesleywmd\Invent\Model\Preference;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements XmlRendererInterface
{
    protected function getType()
    {
        return Location::TYPE_DI;
    }

    protected function getArea(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getArea();
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $dom->updateElement('preference', 'for', $data->getFor())
            ->updateAttribute('type', $data->getType(), ['preference[@for="'.$data->getFor().'"]']);
    }
}