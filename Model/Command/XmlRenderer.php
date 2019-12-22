<?php
namespace Wesleywmd\Invent\Model\Command;

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

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        $typeNodeXpath = $this->addKeyedNode($dom, 'type', 'name', 'Magento\Framework\Console\CommandList');
        $argumentsNodeXpath = $this->addArgumentsNode($dom, $data, $typeNodeXpath);
        $argumentNodeXpath = $this->addKeyedNode($dom, 'argument', 'name', 'commands', $argumentsNodeXpath);
        $this->addItemNode($dom, $data, $argumentNodeXpath);
    }

    private function addArgumentsNode(Dom &$dom, DataInterface $data, $typeNodeXpath)
    {
        $dom->updateElement('arguments', null, null, null, $typeNodeXpath);
        return array_merge($typeNodeXpath, ['arguments']);
    }

    private function addItemNode(Dom &$dom, DataInterface $data, $argumentNodeXpath)
    {
        $itemNodeXpath = array_merge($argumentNodeXpath, ['item[@name="'.$data->getItemName().'"]']);
        $dom->updateElement('item', 'name', $data->getItemName(), $data->getInstance(), $argumentNodeXpath)
            ->updateAttribute('xsi:type', 'object', $itemNodeXpath);
        return $itemNodeXpath;
    }
}