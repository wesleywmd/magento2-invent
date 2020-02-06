<?php
namespace Wesleywmd\Invent\Model\Crud;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $modelName;

    private $noModel;

    public function __construct(ModuleName $moduleName, $modelName, $noModel)
    {
        parent::__construct($moduleName, '', []);
        $this->modelName = $modelName;
        $this->noModel = $noModel;
    }

    public function getModelName()
    {
        return $this->modelName;
    }

    public function getNoModel()
    {
        return $this->noModel;
    }
    
    public function getTableName()
    {
        return $this->moduleName->getSlug([$this->modelName]);
    }
    
    public function getListingDataSourceSlug()
    {
        return $this->moduleName->getSlug([$this->modelName, 'listing', 'data', 'source']);
    }
    
    public function getModelGridCollectionInstance()
    {
        return $this->moduleName->getNamespace(['Model', 'ResourceModel', $this->modelName, 'Grid', 'Collection']);
    }

    public function getResourceModelInstance()
    {
        return $this->moduleName->getNamespace(['Model', 'ResourceModel', $this->modelName]);
    }

    public function getActionsColumnNamespace()
    {
        return $this->moduleName->getNamespace(['Ui', 'Component', 'Listing', 'Column', $this->modelName.'Actions']);
    }

    public function getBackendControllerNamespace()
    {
        return $this->moduleName->getNamespace(['Controller', 'Adminhtml', $this->modelName]);
    }

    public function getButtonNamespace()
    {
        return $this->moduleName->getNamespace(['Block', 'Adminhtml', $this->modelName, 'Button']);
    }

    public function getViewIndexResource()
    {
        return $this->moduleName->getName().'::'.strtolower($this->modelName);
    }

    public function getActionsColumnPath()
    {
        return $this->moduleName->getPath(['Ui', 'Component', 'Listing', 'Column', $this->modelName.'Actions.php']);
    }

    public function getBackButtonPath()
    {
        return $this->moduleName->getPath(['Block', 'Adminhtml', $this->modelName, 'Button', 'Back.php']);
    }

    public function getCreateControllerPath()
    {
        return $this->moduleName->getPath(['Controller', 'Adminhtml', $this->modelName, 'Create.php']);
    }

    public function getIndexControllerPath()
    {
        return $this->moduleName->getPath(['Controller', 'Adminhtml', $this->modelName, 'Index.php']);
    }

    public function getResetButtonPath()
    {
        return $this->moduleName->getPath(['Block', 'Adminhtml', $this->modelName, 'Button', 'Reset.php']);
    }

    public function getSaveButtonPath()
    {
        return $this->moduleName->getPath(['Block', 'Adminhtml', $this->modelName, 'Button', 'Save.php']);
    }
}