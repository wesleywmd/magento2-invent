<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $modelName;

    private $columns;
    
    private $tableName;

    private $noEntityId;

    private $noCreatedAt;

    private $noUpdatedAt;

    public function __construct(ModuleName $moduleName, $modelName, $columns, $tableName, $noEntityId, $noCreatedAt, $noUpdatedAt)
    {
        parent::__construct($moduleName, $modelName, ['Model']);
        $this->modelName = $modelName;
        $this->columns = $columns;
        $this->tableName = $tableName;
        $this->noEntityId = $noEntityId;
        $this->noCreatedAt = $noCreatedAt;
        $this->noUpdatedAt = $noUpdatedAt;
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
    
    public function getVar()
    {
        return strtolower($this->modelName);
    }
    
    public function getIdVar()
    {
        return $this->getVar().'Id';
    }
    
    public function getTable()
    {
        if (!is_null($this->tableName)) {
            return $this->tableName;
        }
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

    public function getRepositoryInstance()
    {
        return $this->moduleName->getNamespace(['Model', $this->modelName.'Repository']);
    }

    public function getSearchResultsInterfaceInstance()
    {
        return $this->moduleName->getNamespace(['Api', 'Data', $this->modelName.'SearchResultsInterface']);
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
    
    public function getInterfacePath()
    {
        return $this->moduleName->getPath(['Api','Data', $this->modelName.'Interface.php']);
    }
    
    public function getResourceModelPath()
    {
        return $this->moduleName->getPath(['Model', 'ResourceModel', $this->modelName.'.php']);
    }
    
    public function getCollectionPath()
    {
        return $this->moduleName->getPath(['Model', 'ResourceModel', $this->modelName, 'Collection.php']);
    }

    public function getSearchResultsInterfacePath()
    {
        return $this->moduleName->getPath(['Api', 'Data', $this->modelName.'SearchResultsInterface.php']);
    }

    public function getRepositoryInterfacePath()
    {
        return $this->moduleName->getPath(['Api', $this->modelName.'RepositoryInterface.php']);
    }

    public function getRepositoryPath()
    {
        return $this->moduleName->getPath(['Model', $this->modelName.'Repository.php']);
    }
}