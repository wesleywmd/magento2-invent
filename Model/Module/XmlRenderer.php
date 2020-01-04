<?php
namespace Wesleywmd\Invent\Model\Module;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements XmlRendererInterface
{
    protected function getType()
    {
        return Location::TYPE_MODULE;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $dom->updateElement('module', 'name', $data->getModuleName()->getName())
        ->updateAttribute('setup_version', '0.0.1', ['module[@name="'.$data->getModuleName()->getName().'"]']);
    }
}