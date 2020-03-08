<?php
namespace Wesleywmd\Invent\Model\Menu;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $menuName;

    private $parentMenu;

    private $action;

    private $resource;

    private $title;

    private $sortOrder;

    private $noAcl;

    public function __construct(
        ModuleName $moduleName,
        $menuName,
        $parentMenu,
        $action,
        $resource = null,
        $title = null,
        $sortOrder = 10,
        $noAcl = false
    ) {
        parent::__construct($moduleName, '', []);
        $this->menuName = $menuName;
        $this->parentMenu = $parentMenu;
        $this->action = $action;
        $this->resource = $resource;
        $this->title = $title;
        $this->sortOrder = $sortOrder;
        $this->noAcl = $noAcl;
    }

    public function getMenuName()
    {
        return $this->menuName;
    }

    public function getParentMenu()
    {
        return $this->parentMenu;
    }

    public function getAction()
    {
        return $this->action;
    }

    public function getResource()
    {
        if (is_null($this->resource)) {
            return $this->getMenuResource();
        }
        return $this->resource;
    }

    public function getTitle()
    {
        if (is_null($this->title)) {
            return implode(' ', array_map( function($name) { return ucfirst($name); }, explode('_', $this->menuName)));
        }
        return $this->title;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    public function getMenuResource()
    {
        return $this->moduleName->getName().'::'.$this->menuName;
    }

    public function getNoAcl()
    {
        return $this->noAcl;
    }
}