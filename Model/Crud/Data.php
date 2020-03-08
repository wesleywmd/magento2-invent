<?php
namespace Wesleywmd\Invent\Model\Crud;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\Menu;
use Wesleywmd\Invent\Model\ModuleName;
use Wesleywmd\Invent\Model\Model;

class Data extends AbstractData implements DataInterface
{
    private $crudName;

    private $noModel;

    private $modelData;

    private $noMenu;

    private $menuData;

    private $routerFrontName;

    public function __construct(
        ModuleName $moduleName,
        $crudName,
        $routerFrontName = null,
        $noModel = false,
        $noMenu = false
    ) {
        parent::__construct($moduleName, '', []);
        $this->crudName = ucfirst($crudName);
        $this->routerFrontName = $routerFrontName;
        $this->noModel = $noModel;
        $this->noMenu = $noMenu;
    }

    public function getCrudName()
    {
        return $this->crudName;
    }

    public function getNoModel()
    {
        return $this->noModel;
    }

    public function hasModel()
    {
        return !is_null($this->modelData);
    }

    public function getModel(): Model\Data
    {
        if (!$this->hasModel()) {
            throw new \Exception('ModelData not saved');
        }
        return $this->modelData;
    }

    public function setModel(Model\Data $modelData)
    {
        $this->modelData = $modelData;
        return $this;
    }

    public function getNoMenu()
    {
        return $this->noMenu;
    }

    public function hasMenu()
    {
        return !is_null($this->menuData);
    }

    public function getMenu(): Menu\Data
    {
        if (!$this->hasMenu()) {
            throw new \Exception('MenuData not saved');
        }
        return $this->menuData;
    }

    public function setMenu(Menu\Data $menuData)
    {
        $this->menuData = $menuData;
        return $this;
    }

    public function getRouterFrontName()
    {
        if (is_null($this->routerFrontName)) {
            return strtolower($this->getCrudName());
        }
        return $this->routerFrontName;
    }

    public function getAdminhtmlControllerNamespace()
    {
        return $this->moduleName->getNamespace(['Controller', 'Adminhtml', $this->getModel()->getModelName()]);
    }

    public function getAdminhtmlControllerPath($controller)
    {
        return $this->moduleName->getPath(['Controller', 'Adminhtml', $this->getModel()->getModelName(), ucfirst($controller).'.php']);
    }

    public function getLayoutHandle($layout)
    {
        return implode('_', [$this->getRouterFrontName(), $this->getModel()->getVar(), $layout]);
    }

    public function getLayoutPath($layout)
    {
        return $this->moduleName->getPath(['view', 'adminhtml', 'layout', $this->getLayoutHandle($layout).'.xml']);
    }

    public function getUiListingName()
    {
        return $this->moduleName->getSlug([$this->getModel()->getVar(), 'listing']);
    }

    public function getUiListingPath()
    {
        return $this->moduleName->getPath(['view', 'adminhtml', 'ui_component', $this->getUiListingName().'.xml']);
    }

    public function getUiListingDataSource($full = false)
    {
        $name = $this->moduleName->getSlug([$this->getModel()->getVar(), 'listing', 'data', 'source']);
        return ((!$full) ? $name : $this->getUiListingName().'.'.$name);
    }

    public function getUiListingSelectProvider()
    {
        return implode('.', [$this->getUiListingName(), $this->getUiListingName(), $this->getUiListingColumns(), 'ids']);
    }

    public function getUiListingColumns($full = false)
    {
        $name = $this->moduleName->getSlug([$this->getModel()->getVar(), 'listing', 'columns']);
        return ((!$full) ? $name : $this->getUiListingName().'.'.$name);
    }

    public function getUiListingColumnsEditor()
    {
        return $this->getUiListingName().'.'.$this->getUiListingColumns(true).'_editor';
    }

    public function getUiFormName()
    {
        return $this->moduleName->getSlug([$this->getModel()->getVar(), 'form']);
    }

    public function getUiFormPath()
    {
        return $this->moduleName->getPath(['view', 'adminhtml', 'ui_component', $this->getUiFormName().'.xml']);
    }

    public function getUiFormDataSource($full = false)
    {
        $name = $this->moduleName->getSlug([$this->getModel()->getVar(), 'form', 'data', 'source']);
        return ((!$full) ? $name : $this->getUiFormName().'.'.$name);
    }

    public function getUiFormSaveButtonClass()
    {
        return $this->moduleName->getNamespace(['Ui', 'Component', $this->getModel()->getModelName(), 'SaveSplitButton']);
    }

    public function getModelGridCollectionInstance()
    {
        return $this->moduleName->getNamespace(['Model', 'ResourceModel', $this->getModel()->getModelName(), 'Grid', 'Collection']);
    }

    public function getUiFormDataProviderClass()
    {
        return $this->moduleName->getNamespace(['Model', $this->getModel()->getModelName(), 'DataProvider']);
    }

    public function getModelDataProviderPath()
    {
        return $this->moduleName->getPath(['Model', $this->getModel()->getModelName(), 'DataProvider.php']);
    }

    public function getModelDataProviderNamespace()
    {
        return $this->moduleName->getNamespace(['Model', $this->getModel()->getModelName()]);
    }

    public function getPersistorKey()
    {
        return $this->moduleName->getSlug();
    }






    public function getActionsColumnNamespace()
    {
        return $this->moduleName->getNamespace(['Ui', 'Component', 'Listing', 'Column', $this->getModel()->getModelName().'Actions']);
    }

    public function getBackendControllerNamespace()
    {
        return $this->moduleName->getNamespace(['Controller', 'Adminhtml', $this->getModel()->getModelName()]);
    }

    public function getButtonNamespace()
    {
        return $this->moduleName->getNamespace(['Block', 'Adminhtml', $this->getModel()->getModelName(), 'Button']);
    }

    public function getViewIndexResource()
    {
        return $this->moduleName->getName().'::'.strtolower($this->getModel()->getModelName());
    }

    public function getActionsColumnPath()
    {
        return $this->moduleName->getPath(['Ui', 'Component', 'Listing', 'Column', $this->getModel()->getModelName().'Actions.php']);
    }

    public function getBackButtonPath()
    {
        return $this->moduleName->getPath(['Block', 'Adminhtml', $this->getModel()->getModelName(), 'Button', 'Back.php']);
    }

    public function getCreateControllerPath()
    {
        return $this->moduleName->getPath(['Controller', 'Adminhtml', $this->getModel()->getModelName(), 'Create.php']);
    }

    public function getIndexControllerPath()
    {
        return $this->moduleName->getPath(['Controller', 'Adminhtml', $this->getModel()->getModelName(), 'Index.php']);
    }

    public function getResetButtonPath()
    {
        return $this->moduleName->getPath(['Block', 'Adminhtml', $this->getModel()->getModelName(), 'Button', 'Reset.php']);
    }

    public function getSaveButtonPath()
    {
        return $this->moduleName->getPath(['Block', 'Adminhtml', $this->getModel()->getModelName(), 'Button', 'Save.php']);
    }
}