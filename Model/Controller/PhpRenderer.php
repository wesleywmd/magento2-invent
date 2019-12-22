<?php
namespace Wesleywmd\Invent\Model\Controller;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class PhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getUseStatements(DataInterface $data)
    {
        return [
            'Magento\Framework\App\Action\Action',
            'Magento\Framework\App\Action\Context',
            'Magento\Framework\View\Result\PageFactory'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->class($data->getClassName())
            ->extend('Action')
            ->setDocComment('/**
                              * TODO implement '.$data->getControllerUrl().' class body
                              */')
            ->addStmt($this->phpBuilder->property('resultPageFactory')->makeProtected())
            ->addStmt($this->phpBuilder->constructor(['context'=>'Context', 'resultPageFactory'=>'PageFactory'], true, ['context']))
            ->addStmt($this->getExecuteMethod($data));
    }

    private function getExecuteMethod(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmt($this->phpBuilder->assign(
                $this->phpBuilder->var('resultPage'),
                $this->phpBuilder->methodCall(
                    $this->phpBuilder->thisPropertyFetch('resultPageFactory'),
                    'create'
                )
            ))
            ->addStmt($this->phpBuilder->returnStmt($this->phpBuilder->var('resultPage')));
    }
}