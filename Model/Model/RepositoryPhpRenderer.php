<?php
namespace Wesleywmd\Invent\Model\Model;

use PhpParser\Node\Expr\BooleanNot;
use PhpParser\Node\Stmt\If_;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class RepositoryPhpRenderer implements PhpRendererInterface
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
        return $this->phpBuilder->namespace($data->getModuleName()->getNamespace(['Model']))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Api', 'Data', $data->getModelName() . 'Interface'])))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Api', $data->getModelName() . 'RepositoryInterface'])))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Api', 'Data', $data->getModelName() . 'SearchResultsInterfaceFactory'])))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Model', 'ResourceModel', $data->getModelName()]))->as($data->getModelName() . 'Resource'))
            ->addStmt($this->phpBuilder->use($data->getModuleName()->getNamespace(['Model', 'ResourceModel', $data->getModelName(), 'CollectionFactory'])))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\FilterFactory'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\Search\FilterGroupFactory'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\SearchCriteriaFactory'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\SearchCriteriaInterface'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\SearchCriteria\CollectionProcessorInterface'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Exception\CouldNotDeleteException'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Exception\CouldNotSaveException'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Exception\NoSuchEntityException'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\SearchCriteriaInterface'))
            ->addStmt($this->phpBuilder->use('Magento\Framework\Api\SearchCriteriaInterface'))
            ->addStmt($this->getClassStatement($data))
            ->getNode();
    }

    private function getClassStatement(Data $data)
    {
        return $this->phpBuilder->class($data->getModelName() . 'Repository')
            ->addStmt($this->phpBuilder->property('resource')->makePrivate())
            ->addStmt($this->phpBuilder->property($data->getModelName() . 'Factory')->makePrivate())
            ->addStmt($this->phpBuilder->property($data->getModelName() . 'CollectionFactory')->makePrivate())
            ->addStmt($this->phpBuilder->property('collectionProcessor')->makePrivate())
            ->addStmt($this->phpBuilder->property('searchResultsFactory')->makePrivate())
            ->addStmt($this->phpBuilder->property('searchCriteriaFactory')->makePrivate())
            ->addStmt($this->phpBuilder->property('filterFactory')->makePrivate())
            ->addStmt($this->phpBuilder->property('filterGroupFactory')->makePrivate())
            ->addStmt($this->phpBuilder->constructor([
                'resource' => $data->getModelName() . 'Resource',
                $data->getModelVarName() . 'Factory' => $data->getModelName() . 'Factory',
                $data->getModelVarName() . 'CollectionFactory' => 'CollectionFactory',
                'collectionProcessor' => 'CollectionProcessorInterface',
                'searchResultsFactory' => $data->getModelName() . 'SearchResultsInterfaceFactory',
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
        $modelVar = $this->phpBuilder->var($data->getModelVarName());
        $exceptionVar = $this->phpBuilder->var('exception');
        $throwNewStmt = $this->phpBuilder->throwNew('CouldNotSaveException', [
            $this->getTranslateFuncCall('Could not save the '.$data->getModelName().': %1', [$this->phpBuilder->methodCall($exceptionVar, 'getMessage')]),
            $exceptionVar
        ]);
        return $this->phpBuilder->method('save')
            ->makePublic()
            ->addParam($this->phpBuilder->param($data->getModelVarName())->setType($data->getModelName() . 'Interface'))
            ->addStmt($this->phpBuilder->tryCatch([
                $this->phpBuilder->methodCall($this->getThisFetch('resource'), 'save', [$this->phpBuilder->nodeArg($modelVar)])
            ], [
                $this->phpBuilder->catch('\Exception', 'exception', [$throwNewStmt])
            ]))
            ->addStmt($this->phpBuilder->returnStmt($modelVar));
    }

    private function getGetByIdMethodStatement(Data $data)
    {
        $modelVar = $this->phpBuilder->var($data->getModelVarName());
        $modelIdVar = $this->phpBuilder->var($data->getModelIdVarName());
        $exceptionVar = $this->phpBuilder->var('exception');
        $createMethodCall = $this->phpBuilder->methodCall($this->getThisFetch( $data->getModelVarName().'Factory'), 'create');
        $throwNewStmt = $this->phpBuilder->throwNew('NoSuchEntityException', [
            $this->getTranslateFuncCall($data->getModelName().' with id "%1" does not exist.', [$modelIdVar])
        ]);
        $booleanNot = $this->phpBuilder->booleanNot($this->phpBuilder->methodCall($modelVar, 'getId'));
        return $this->phpBuilder->method('getById')
            ->makePublic()
            ->addParam($this->phpBuilder->param($data->getModelIdVarName()))
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
            ->addParam($this->phpBuilder->param('searchCriteriaInterface')->setType('searchCriteria'))
            ->addStmt($this->phpBuilder->assign($collectionVar,
                $this->phpBuilder->methodCall($this->getThisFetch($data->getModelVarName().'CollectionFactory'), 'create')
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
            ->addParam($this->phpBuilder->param($data->getModelVarName())->setType($data->getModelName() . 'Interface'))
            ->addStmt($this->phpBuilder->tryCatch([
                $this->phpBuilder->methodCall($this->getThisFetch('resource'), 'delete', [
                    $this->phpBuilder->nodeArg($this->phpBuilder->var($data->getModelVarName()))
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
            ->addParam($this->phpBuilder->param($data->getModelIdVarName()))
            ->addStmt($this->phpBuilder->returnStmt($this->phpBuilder->methodCall($this->phpBuilder->var('this'), 'delete', [
                $this->phpBuilder->methodCall($this->phpBuilder->var('this'), 'getById', [
                    $this->phpBuilder->var($data->getModelIdVarName())
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
