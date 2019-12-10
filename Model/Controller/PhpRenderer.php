<?php
namespace Wesleywmd\Invent\Model\Controller;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class PhpRenderer implements PhpRendererInterface
{
    private $phpBuilder;

    private $prettyPrinter;

    public function __construct(PhpBuilder $phpBuilder, PrettyPrinter $prettyPrinter)
    {
        $this->phpBuilder = $phpBuilder;
        $this->prettyPrinter = $prettyPrinter;
    }

    public function getContents(DataInterface $data)
    {
        return $this->prettyPrinter->print([$this->getBuilderNode($data)]);
    }

    private function getBuilderNode(Data $data)
    {
        return $this->phpBuilder->namespace($data->getNamespace())
            ->addStmt($this->phpBuilder->use('Magento\\Framework\\App\\Action\\Action'))
            ->addStmt($this->phpBuilder->use('Magento\\Framework\\App\\Action\\Context'))
            ->addStmt($this->phpBuilder->use('Magento\\Framework\\View\\Result\\PageFactory'))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        return $this->phpBuilder->class($data->getClassName())
            ->extend('Action')
            ->setDocComment('/**
                              * TODO implement '.$data->getControllerUrl().' class body
                              */')
            ->addStmt($this->phpBuilder->property('resultPageFactory')->makeProtected())
            ->addStmt($this->phpBuilder->constructor(['context'=>'Context', 'resultPageFactory'=>'PageFactory'], true, ['context']))
            ->addStmt($this->getExecuteMethod($data));
    }

    private function getExecuteMethod(Data $data)
    {
        $resultPageVar = $this->phpBuilder->var('resultPage');
        $thisVar = $this->phpBuilder->var('this');
        $resultPageFetch = $this->phpBuilder->propertyFetch($thisVar, 'resultPageFactory');
        $createMethodCall = $this->phpBuilder->methodCall($resultPageFetch, 'create');
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmt($this->phpBuilder->assign($resultPageVar, $createMethodCall))
            ->addStmt($this->phpBuilder->returnStmt($resultPageVar));
    }
}