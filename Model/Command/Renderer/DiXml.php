<?php
namespace Wesleywmd\Invent\Model\Command\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\BaseDiXml;
use Wesleywmd\Invent\Model\XmlParser\Dom;

class DiXml extends BaseDiXml implements RendererInterface
{
    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        $this->addType($dom, 'Magento\Framework\Console\CommandList', [
            'commands' => [
                $data->getItemName() => $data->getInstance()
            ]
        ]);
    }
}