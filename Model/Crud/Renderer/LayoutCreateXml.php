<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class LayoutCreateXml extends AbstractXmlRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getLayoutPath('create');
    }

    protected function getType()
    {
        return Location::TYPE_LAYOUT;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Crud\Data $data */
        $dom->updateElement('update', 'handle', 'styles')
            ->updateElement('update', 'handle', 'editor');
        $this->addBodyNode($dom, $data->getUiFormName());
    }

    private function addBodyNode(Dom &$dom, $uiComponentName)
    {
        $bodyXpath = ['body'];
        $referenceContainerXpath = array_merge($bodyXpath, ['referenceContainer[@name="content"]']);
        $dom->updateElement('body')
            ->updateElement('referenceContainer', 'name', 'content', null, $bodyXpath)
            ->updateElement('uiComponent', 'name', $uiComponentName, null, $referenceContainerXpath);
    }
}