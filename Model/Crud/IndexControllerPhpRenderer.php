<?php
namespace Wesleywmd\Invent\Model\Crud;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class IndexControllerPhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getNamespace(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getBackendControllerNamespace();
    }

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Data $data */
        return [
            'Magento\Backend\App\Action',
            'Magento\Framework\View\Result\PageFactory'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->class('Index')
            ->extend('Action')
            ->addStmt($this->phpBuilder->property('resultPageFactory')->makeProtected())
            ->addStmt($this->phpBuilder->constructor([
                'context'=>'Action\Context',
                'resultPageFactory'=>'PageFactory'
            ], true, ['context']))
            ->addStmt($this->getExecuteMethod($data))
            ->addStmt($this->getIsAllowedMethod($data));
    }

    protected function getExecuteMethod(DataInterface $data)
    {
        /** @var Data $data */
        $resultPageVar = $this->phpBuilder->var('resultPage');
        $thisVar = $this->phpBuilder->var('this');
        $resultPageFetch = $this->phpBuilder->propertyFetch($thisVar, 'resultPageFactory');
        $createMethodCall = $this->phpBuilder->methodCall($resultPageFetch, 'create');
        $getConfigMethodCall = $this->phpBuilder->methodCall($resultPageVar, 'getConfig');
        $getTitleMethodCall = $this->phpBuilder->methodCall($getConfigMethodCall, 'getTitle');
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmt($this->phpBuilder->assign($resultPageVar, $createMethodCall))
            ->addStmt($this->phpBuilder->methodCall($getTitleMethodCall, 'prepend', [
                $this->phpBuilder->funcCall('__', [$this->phpBuilder->val($data->getModel()->getClassName())])
            ]))
            ->addStmt($this->phpBuilder->returnStmt($resultPageVar));
    }
    
    protected function getIsAllowedMethod(DataInterface $data)
    {
        /** @var Data $data */
        $thisVar = $this->phpBuilder->var('this');
        $authFetch = $this->phpBuilder->propertyFetch($thisVar, '_authorization');
        $isAllowedFetch = $this->phpBuilder->methodCall($authFetch, 'isAllowed', [
            $this->phpBuilder->val($data->getViewIndexResource())
        ]);
        return $this->phpBuilder->method('_isAllowed')->makeProtected()
            ->addStmt($this->phpBuilder->returnStmt($isAllowedFetch));
    }
}