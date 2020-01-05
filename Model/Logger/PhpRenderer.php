<?php
namespace Wesleywmd\Invent\Model\Logger;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class PhpRenderer extends AbstractPhpRenderer implements RendererInterface
{
    protected function getClassStatement(DataInterface $data)
    {
        return $this->phpBuilder->class('Logger')
            ->extend('\Monolog\Logger');
    }
}