<?php
namespace Wesleywmd\Invent\Model\Logger;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements RendererInterface
{
    protected function getType()
    {
        return Location::TYPE_DI;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $typeNodeXpath = $this->addKeyedNode($dom, 'type', 'name', $data->getHandlerInstance());
        $argumentsNodeXpath = $this->addArgumentsNode($dom, $data, $typeNodeXpath);
        $this->addFilesystemArgument($dom, $data, $argumentsNodeXpath);

        $typeNodeXpath = $this->addKeyedNode($dom, 'type', 'name', $data->getInstance());
        $argumentsNodeXpath = $this->addArgumentsNode($dom, $data, $typeNodeXpath);
        $argumentNodeXpath = $this->addNameArgument($dom, $data, $argumentsNodeXpath);
        $argumentNodeXpath = $this->addHandlersArgument($dom, $data, $argumentsNodeXpath);
        $this->addItemNode($dom, $data, $argumentNodeXpath);
    }

    private function addArgumentsNode(Dom &$dom, DataInterface $data, $typeNodeXpath)
    {
        $dom->updateElement('arguments', null, null, null, $typeNodeXpath);
        return array_merge($typeNodeXpath, ['arguments']);
    }

    private function addFilesystemArgument(Dom &$dom, DataInterface $data, $argumentsNodeXpath)
    {
        $argumentNodeXpath = array_merge($argumentsNodeXpath, ['argument[@name="filesystem"]']);
        $dom->updateElement('argument', 'name', 'filesystem', 'Magento\Framework\Filesystem\Driver\File', $argumentsNodeXpath)
            ->updateAttribute('xsi:type', 'object', $argumentNodeXpath);
        return $argumentNodeXpath;
    }

    private function addNameArgument(Dom &$dom, DataInterface $data, $argumentsNodeXpath)
    {
        /** @var Data $data */
        $argumentNodeXpath = array_merge($argumentsNodeXpath, ['argument[@name="name"]']);
        $dom->updateElement('argument', 'name', 'name', $data->getLoggerName(), $argumentsNodeXpath)
            ->updateAttribute('xsi:type', 'string', $argumentNodeXpath);
        return $argumentNodeXpath;
    }

    private function addHandlersArgument(Dom &$dom, DataInterface $data, $argumentsNodeXpath)
    {
        $argumentNodeXpath = array_merge($argumentsNodeXpath, ['argument[@name="handlers"]']);
        $dom->updateElement('argument', 'name', 'handlers', null, $argumentsNodeXpath)
            ->updateAttribute('xsi:type', 'array', $argumentNodeXpath);
        return $argumentNodeXpath;
    }

    private function addItemNode(Dom &$dom, DataInterface $data, $argumentNodeXpath)
    {
        /** @var Data $data */
        $itemNodeXpath = array_merge($argumentNodeXpath, ['item[@name="system"]']);
        $dom->updateElement('item', 'name', 'system', $data->getHandlerInstance(), $argumentNodeXpath)
            ->updateAttribute('xsi:type', 'object', $itemNodeXpath);
        return $itemNodeXpath;
    }
}