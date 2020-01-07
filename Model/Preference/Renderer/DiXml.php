<?php
namespace Wesleywmd\Invent\Model\Preference\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\BaseDiXml;
use Wesleywmd\Invent\Model\XmlParser\Dom;

class DiXml extends BaseDiXml implements RendererInterface
{
    protected function getArea(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getArea();
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $this->addPreference($dom, $data->getFor(), $data->getType());
    }
}