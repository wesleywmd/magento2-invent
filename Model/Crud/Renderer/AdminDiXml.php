<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\BaseDiXml;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class AdminDiXml extends BaseDiXml implements RendererInterface
{
    protected function getArea(DataInterface $data)
    {
        return Location::AREA_ADMINHTML;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Crud\Data $data */
        $this->addVirtualType($dom,
            $data->getModuleName()->getNamespace(['Ui', 'Component', $data->getModel()->getModelName(), 'SaveSplitButton']),
            'Magento\Backend\Ui\Component\Control\SaveSplitButton',
            ['targetName' => $data->getUiFormName().'.'.$data->getUiFormName()]
        );
    }
}