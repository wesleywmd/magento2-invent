<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class RepositoryInterfacePhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    protected function getNamespace(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getModuleName()->getNamespace(['Api']);
    }

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Data $data */
        return [
            'Magento\Framework\Api\SearchCriteriaInterface',
            $data->getInterfaceInstance()
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->interface($data->getModelName().'RepositoryInterface')
            ->addStmt($this->phpBuilder->method('save')->makePublic()
                ->addParam($this->phpBuilder->param($data->getModelVarName())->setType($data->getInterfaceName()))
            )
            ->addStmt($this->phpBuilder->method('getById')->makePublic()
                ->addParam($this->phpBuilder->param($data->getModelIdVarName()))
            )
            ->addStmt($this->phpBuilder->method('getList')->makePublic()
                ->addParam($this->phpBuilder->param('searchCriteria')->setType('SearchCriteriaInterface'))
            )
            ->addStmt($this->phpBuilder->method('delete')->makePublic()
                ->addParam($this->phpBuilder->param($data->getModelVarName())->setType($data->getInterfaceName()))
            )
            ->addStmt($this->phpBuilder->method('deleteById')->makePublic()
                ->addParam($this->phpBuilder->param($data->getModelIdVarName()))
            );
    }
}