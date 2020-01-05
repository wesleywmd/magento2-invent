<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;

class RepositoryPhpRenderer extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getRepositoryPath();
    }
    
    protected function getNamespace(DataInterface $data)
    {
        /** @var Data $data */
        return $data->getModuleName()->getNamespace(['Model']);
    }

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Data $data */
        return [
            $data->getInterfaceInstance(),
            $data->getRepositoryInterfaceInstance(),
            $data->getSearchResultsInterfaceFactoryInstance(),
            $data->getResourceModelName() => $data->getResourceModelInstance(),
            $data->getCollectionFactoryInstance(),
            'Magento\Framework\Api\FilterFactory',
            'Magento\Framework\Api\Search\FilterGroupFactory',
            'Magento\Framework\Api\SearchCriteriaFactory',
            'Magento\Framework\Api\SearchCriteriaInterface',
            'Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface',
            'Magento\Framework\Exception\CouldNotDeleteException',
            'Magento\Framework\Exception\CouldNotSaveException',
            'Magento\Framework\Exception\NoSuchEntityException'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Data $data */
        return $this->phpBuilder->class($data->getModelName().'Repository')
            ->implement($data->getModelName().'RepositoryInterface')
            ->addStmt($this->phpBuilder->property('resource')->makePrivate())
            ->addStmt($this->phpBuilder->property($data->getVar().'Factory')->makePrivate())
            ->addStmt($this->phpBuilder->property($data->getVar().'CollectionFactory')->makePrivate())
            ->addStmt($this->phpBuilder->property('collectionProcessor')->makePrivate())
            ->addStmt($this->phpBuilder->property('searchResultsFactory')->makePrivate())
            ->addStmt($this->phpBuilder->property('searchCriteriaFactory')->makePrivate())
            ->addStmt($this->phpBuilder->property('filterFactory')->makePrivate())
            ->addStmt($this->phpBuilder->property('filterGroupFactory')->makePrivate())
            ->addStmt($this->phpBuilder->constructor([
                'resource' => $data->getResourceModelName(),
                $data->getVar().'Factory' => $data->getModelName().'Factory',
                $data->getVar().'CollectionFactory' => 'CollectionFactory',
                'collectionProcessor' => 'CollectionProcessorInterface',
                'searchResultsFactory' => $data->getModelName().'SearchResultsInterfaceFactory',
                'searchCriteriaFactory' => 'SearchCriteriaFactory',
                'filterFactory' => 'FilterFactory',
                'filterGroupFactory' => 'FilterGroupFactory'
            ]))
            ->addStmt($this->getSaveMethodStatement($data))
            ->addStmt($this->getGetByIdMethodStatement($data))
            ->addStmt($this->getGetListMethodStatement($data))
            ->addStmt($this->getDeleteMethodStatement($data))
            ->addStmt($this->getDeleteByIdMethodStatement($data));
    }

    private function getSaveMethodStatement(Data $data)
    {
        $modelVar = $this->phpBuilder->var($data->getVar());
        $exceptionVar = $this->phpBuilder->var('exception');
        $throwNewStmt = $this->phpBuilder->throwNew('CouldNotSaveException', [
            $this->getTranslateFuncCall('Could not save the '.$data->getModelName().': %1', [
                $this->phpBuilder->methodCall($exceptionVar, 'getMessage')
            ]), 
            $exceptionVar
        ]);
        return $this->phpBuilder->method('save')
            ->makePublic()
            ->addParam($this->phpBuilder->param($data->getVar())->setType($data->getInterfaceName()))
            ->addStmt($this->phpBuilder->tryCatch([
                $this->phpBuilder->methodCall($this->getThisFetch('resource'), 'save', [
                    $this->phpBuilder->nodeArg($modelVar)
                ])
            ], [
                $this->phpBuilder->catch('\Exception', 'exception', [$throwNewStmt])
            ]))
            ->addStmt($this->phpBuilder->returnStmt($modelVar));
    }

    private function getGetByIdMethodStatement(Data $data)
    {
        $modelVar = $this->phpBuilder->var($data->getVar());
        $modelIdVar = $this->phpBuilder->var($data->getIdVar());
        $exceptionVar = $this->phpBuilder->var('exception');
        $createMethodCall = $this->phpBuilder->methodCall($this->getThisFetch( $data->getVar().'Factory'), 'create');
        $throwNewStmt = $this->phpBuilder->throwNew('NoSuchEntityException', [
            $this->getTranslateFuncCall($data->getModelName().' with id "%1" does not exist.', [$modelIdVar])
        ]);
        $booleanNot = $this->phpBuilder->booleanNot($this->phpBuilder->methodCall($modelVar, 'getId'));
        return $this->phpBuilder->method('getById')
            ->makePublic()
            ->addParam($this->phpBuilder->param($data->getIdVar()))
            ->addStmt($this->phpBuilder->assign($modelVar, $createMethodCall))
            ->addStmt($this->phpBuilder->methodCall($this->getThisFetch('resource'), 'load', [$modelVar, $modelIdVar]))
            ->addStmt($this->phpBuilder->if($booleanNot, ['stmts' => [$throwNewStmt]]))
            ->addStmt($this->phpBuilder->returnStmt($modelVar));
    }

    private function getGetListMethodStatement(Data $data)
    {
        $collectionVar = $this->phpBuilder->var('collection');
        $searchCriteriaVar = $this->phpBuilder->var('searchCriteria');
        $searchResultsVar = $this->phpBuilder->var('searchResults');
        return $this->phpBuilder->method('getList')
            ->makePublic()
            ->addParam($this->phpBuilder->param('searchCriteria')->setType('SearchCriteriaInterface'))
            ->addStmt($this->phpBuilder->assign($collectionVar,
                $this->phpBuilder->methodCall($this->getThisFetch($data->getVar().'CollectionFactory'), 'create')
            ))
            ->addStmt($this->phpBuilder->methodCall(
                $this->getThisFetch('collectionProcessor'), 'process', [$searchCriteriaVar, $collectionVar])
            )
            ->addStmt($this->phpBuilder->assign($searchResultsVar,
                $this->phpBuilder->methodCall($this->getThisFetch('searchResultFactory'), 'create')
            ))
            ->addStmt($this->phpBuilder->methodCall($searchResultsVar, 'setSearchCriteria', [$searchCriteriaVar]))
            ->addStmt($this->phpBuilder->methodCall($searchResultsVar, 'setItems', [
                $this->phpBuilder->methodCall($collectionVar, 'getItems')
            ]))
            ->addStmt($this->phpBuilder->methodCall($searchResultsVar, 'setTotalCount', [
                $this->phpBuilder->methodCall($collectionVar, 'getSize')
            ]))
            ->addStmt($this->phpBuilder->returnStmt($searchResultsVar));
    }

    private function getDeleteMethodStatement(Data $data)
    {
        $throwNewStmt = $this->phpBuilder->throwNew('CouldNotDeleteException', [
            $this->getTranslateFuncCall('Could not delete the '.$data->getModelName().': %1', [
                $this->phpBuilder->methodCall($this->phpBuilder->var('exception'), 'getMessage')]),
            $this->phpBuilder->var('exception')
        ]);
        return $this->phpBuilder->method('delete')
            ->makePublic()
            ->addParam($this->phpBuilder->param($data->getVar())->setType($data->getModelName() . 'Interface'))
            ->addStmt($this->phpBuilder->tryCatch([
                $this->phpBuilder->methodCall($this->getThisFetch('resource'), 'delete', [
                    $this->phpBuilder->nodeArg($this->phpBuilder->var($data->getVar()))
                ])
            ], [
                $this->phpBuilder->catch('\Exception', 'exception', [$throwNewStmt])
            ]))
            ->addStmt($this->phpBuilder->returnStmt($this->phpBuilder->val(true)));
    }

    private function getDeleteByIdMethodStatement(Data $data)
    {
        return $this->phpBuilder->method('deleteById')
            ->makePublic()
            ->addParam($this->phpBuilder->param($data->getIdVar()))
            ->addStmt($this->phpBuilder->returnStmt($this->phpBuilder->methodCall($this->phpBuilder->var('this'), 'delete', [
                $this->phpBuilder->methodCall($this->phpBuilder->var('this'), 'getById', [
                    $this->phpBuilder->var($data->getIdVar())
                ])
            ])));
    }

    private function getThisFetch($name)
    {
        return $this->phpBuilder->propertyFetch($this->phpBuilder->var('this'), $name);
    }

    private function getTranslateFuncCall($string, $args = [])
    {
        return $this->phpBuilder->funcCall('__', array_merge([$this->phpBuilder->val($string)], $args));
    }
}
