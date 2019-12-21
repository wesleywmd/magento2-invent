<?php
namespace Wesleywmd\Invent\Model\Block;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class PhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getUseStatements(DataInterface $data)
    {
        return ['Magento\Framework\View\Element\Template'];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->class($data->getClassName())
            ->extend('Template')
            ->setDocComment('/**
                              * TODO implement '.$data->getBlockName().' class body
                              */');
    }
}