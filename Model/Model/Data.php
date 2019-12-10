<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\ModuleName;

class Data implements DataInterface
{
    private $moduleName;

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
    }

    public function getModuleName()
    {
        return $this->moduleName;
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
}