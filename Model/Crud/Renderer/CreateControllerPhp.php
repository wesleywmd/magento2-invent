<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class CreateControllerPhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        return $data->getCreateControllerPath(); 
    }

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
        return $this->phpBuilder->class('Create')
            ->extend('Action')
            ->addStmt($this->phpBuilder->property('resultPageFactory')->makeProtected())
            ->addStmt($this->phpBuilder->constructor([
                'context'=>'Action\Context',
                'resultPageFactory'=>'PageFactory'
            ], true, ['context']))
            ->addStmt($this->getExecuteMethod($data));
    }

    protected function getExecuteMethod(DataInterface $data)
    {
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
                $this->phpBuilder->funcCall('__', [$this->phpBuilder->val('Create New '.$data->getModelName())])
            ]))
            ->addStmt($this->phpBuilder->returnStmt($resultPageVar));
    }
}