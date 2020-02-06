<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class CreateLayoutXml extends AbstractXmlRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        $fileName = $data->getModuleName()->getSlug([strtolower($data->getModelName()), 'create']).'.xml';
        return $data->getModuleName()->getPath(['view', 'adminhtml', 'layout', $fileName]);
    }

    protected function getType()
    {
        return Location::TYPE_LAYOUT;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        $uiComponentName = $data->getModuleName()->getSlug([$data->getModel()->getVar(), 'form', 'create']);
        $dom->updateElement('update', 'handle', 'styles')
            ->updateElement('update', 'handle', 'editor')
            ->updateElement('body')
            ->updateElement('referenceContainer', 'name', 'content', null, ['body'])
            ->updateElement('uiComponent', 'name', $uiComponentName, null, ['body', 'referenceContainer[@name="content"]']);
    }
}