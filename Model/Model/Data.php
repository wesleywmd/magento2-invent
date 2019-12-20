<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $modelName;

    private $columns;

    private $noEntityId;

    private $noCreatedAt;

    private $noUpdatedAt;

    public function __construct(ModuleName $moduleName, $modelName, $columns, $noEntityId, $noCreatedAt, $noUpdatedAt)
    {
        $this->moduleName = $moduleName;
        $this->modelName = $modelName;
        $this->columns = $columns;
        $this->noEntityId = $noEntityId;
        $this->noCreatedAt = $noCreatedAt;
        $this->noUpdatedAt = $noUpdatedAt;
        $this->className = $modelName;
        $this->directories = ['Model'];

    }

    public function getModelName()
    {
        return $this->modelName;
    }

    public function getColumns()
    {
        return $this->columns;
    }

    public function getNoEntityId()
    {
        return $this->noEntityId;
    }

    public function getNoCreatedAt()
    {
        return $this->noCreatedAt;
    }

    public function getNoUpdatedAt()
    {
        return $this->noUpdatedAt;
    }

    public function getModelVarName()
    {
        return strtolower($this->modelName);
    }

    public function getModelIdVarName()
    {
        return $this->getModelVarName().'Id';
    }

    public function getTableName()
    {
        return $this->moduleName->getSlug([$this->modelName]);
    }

    public function getInterfaceInstance()
    {
        return $this->moduleName->getNamespace(['Api', 'Data', $this->modelName.'Interface']);
    }

    public function getRepositoryInterfaceInstance()
    {
        return $this->moduleName->getNamespace(['Api', $this->modelName.'RepositoryInterface']);
    }

    public function getSearchResultsInterfaceFactoryInstance()
    {
        return $this->moduleName->getNamespace(['Api', 'Data', $this->modelName.'SearchResultsInterfaceFactory']);
    }

    public function getResourceModelInstance()
    {
        return $this->moduleName->getNamespace(['Model', 'ResourceModel', $this->modelName]);
    }

    public function getCollectionFactoryInstance()
    {
        return $this->moduleName->getNamespace(['Model', 'resourceModel', $this->modelName, 'CollectionFactory']);
    }

    public function getResourceModelName()
    {
        return $this->modelName.'Resource';
    }

    public function getInterfaceName()
    {
        return $this->modelName.'Interface';
    }
}