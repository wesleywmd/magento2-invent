<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class ResetButtonPhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        return $data->getResetButtonPath();
    }

    protected function getNamespace(DataInterface $data)
    {
        return $data->getButtonNamespace();
    }

    protected function getUseStatements(DataInterface $data)
    {
        return ['Magento\Framework\View\Element\UiComponent\Control\ButtonProviderInterface'];
    }

    protected function getClassStatement(DataInterface $data)
    {
        return $this->phpBuilder->class('Reset')
            ->implement('ButtonProviderInterface')
            ->addStmt($this->getButtonDataMethod($data));
    }

    protected function getButtonDataMethod($data)
    {
        return $this->phpBuilder->method('getButtonData')->makePublic()
            ->addStmt($this->phpBuilder->returnStmt($this->phpBuilder->val([
                'label' => $this->phpBuilder->funcCall('__', [$this->phpBuilder->val('Reset')]),
                'on_click' => $this->phpBuilder->val('location.reload();'),
                'class' => $this->phpBuilder->val('reset'),
                'sort_order' => $this->phpBuilder->val(30)
            ])));
    }
}