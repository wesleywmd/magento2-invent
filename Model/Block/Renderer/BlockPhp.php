<?php
namespace Wesleywmd\Invent\Model\Block\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class BlockPhp extends AbstractPhpRenderer implements RendererInterface
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