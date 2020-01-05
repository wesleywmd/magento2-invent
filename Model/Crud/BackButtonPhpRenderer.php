<?php
namespace Wesleywmd\Invent\Model\Crud;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class BackButtonPhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getNamespace(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getButtonNamespace();
    }

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Data $data */
        return [
            'Magento\Backend\Block\Widget\Context',
            'Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->class('BackButton')
            ->implement('ButtonProviderInterface')
            ->addStmt($this->phpBuilder->property('urlBuilder')->makeProtected())
            ->addStmt($this->getConstructor($data))
            ->addStmt($this->getButtonDataMethod($data));
    }

    protected function getConstructor(DataInterface $data)
    {
        $urlBuilderVar = $this->phpBuilder->propertyFetch($this->phpBuilder->var('this'), 'urlBuilder');
        $contextBuilderFetch = $this->phpBuilder->methodCall($this->phpBuilder->var('context'), 'getUrlBuilder');
        return $this->phpBuilder->method('__construct')->makePublic()
            ->addParam($this->phpBuilder->param('context')->setType('Context'))
            ->addStmt($this->phpBuilder->assign($urlBuilderVar, $contextBuilderFetch));
    }

    protected function getButtonDataMethod($data)
    {
        $sprintf = $this->phpBuilder->funcCall('sprintf', [
            $this->phpBuilder->val('location.href = \'%s\';'),
            $this->phpBuilder->methodCall($this->phpBuilder->thisPropertyFetch('urlBuilder'), 'getUrl', [
                $this->phpBuilder->val('*/*/')
            ])
        ]);
        return $this->phpBuilder->method('getButtonData')->makePublic()
            ->addStmt($this->phpBuilder->returnStmt($this->phpBuilder->val([
                'label' => $this->phpBuilder->funcCall('__', [$this->phpBuilder->val('Back')]),
                'on_click' => $sprintf,
                'class' => $this->phpBuilder->val('back'),
                'sort_order' => $this->phpBuilder->val(10)
            ])));
    }
}