<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class RepositoryInterfacePhpRenderer extends AbstractPhpRenderer implements PhpRendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getRepositoryInterfacePath();
    }
    
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
                ->addParam($this->phpBuilder->param($data->getVar())->setType($data->getInterfaceName()))
            )
            ->addStmt($this->phpBuilder->method('getById')->makePublic()
                ->addParam($this->phpBuilder->param($data->getIdVar()))
            )
            ->addStmt($this->phpBuilder->method('getList')->makePublic()
                ->addParam($this->phpBuilder->param('searchCriteria')->setType('SearchCriteriaInterface'))
            )
            ->addStmt($this->phpBuilder->method('delete')->makePublic()
                ->addParam($this->phpBuilder->param($data->getVar())->setType($data->getInterfaceName()))
            )
            ->addStmt($this->phpBuilder->method('deleteById')->makePublic()
                ->addParam($this->phpBuilder->param($data->getIdVar()))
            );
    }
}