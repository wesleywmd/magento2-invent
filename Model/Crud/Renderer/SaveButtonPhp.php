<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class SaveButtonPhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        return $data->getSaveButtonPath();
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
        return $this->phpBuilder->class('Save')
            ->implement('ButtonProviderInterface')
            ->addStmt($this->getButtonDataMethod($data));
    }

    protected function getButtonDataMethod($data)
    {
        return $this->phpBuilder->method('getButtonData')->makePublic()
            ->addStmt($this->phpBuilder->returnStmt($this->phpBuilder->val([
                'label' => $this->phpBuilder->funcCall('__', [$this->phpBuilder->val('Save '.$data->getModelName())]),
                'class' => $this->phpBuilder->val('save primary'),
                'data_attribute' => [
                    'mage-init' => [
                        'button' => [
                            'event' => $this->phpBuilder->val('save')
                        ]
                    ],
                    'form-role' => $this->phpBuilder->val('save'),
                ],
                'sort_order' => $this->phpBuilder->val(90)
            ])));
    }
}