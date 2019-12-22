<?php
namespace Wesleywmd\Invent\Model\Logger;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class PhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getClassStatement(DataInterface $data)
    {
        return $this->phpBuilder->class('Logger')
            ->extend('\Monolog\Logger');
    }
}