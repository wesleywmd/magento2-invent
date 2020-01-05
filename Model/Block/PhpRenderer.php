<?php
namespace Wesleywmd\Invent\Model\Block;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class PhpRenderer extends AbstractPhpRenderer implements RendererInterface
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