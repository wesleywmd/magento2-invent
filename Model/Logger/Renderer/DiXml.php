<?php
namespace Wesleywmd\Invent\Model\Logger\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\BaseDiXml;
use Wesleywmd\Invent\Model\XmlParser\Dom;

class DiXml extends BaseDiXml implements RendererInterface
{
    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $this->addType($dom, $data->getHandlerInstance(), [
            'filesystem' => 'Magento\Framework\Filesystem\Driver\File'
        ]);

        $this->addType($dom, $data->getInstance(), [
            'name' => $data->getLoggerName(),
            'handlers' => [
                'system' => $data->getHandlerInstance()
            ]
        ]);
    }
}