<?php
namespace Wesleywmd\Invent\Model\Acl;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\ModuleName;

class Data implements DataInterface
{
    private $moduleName;

    private $aclName;

    private $parentAcl;

    private $title;

    private $sortOrder;

    public function __construct(ModuleName $moduleName, $aclName, $parentAcl = null, $title = null, $sortOrder = 10)
    {
        $this->moduleName = $moduleName;
        $this->aclName = $aclName;
        $this->parentAcl = $parentAcl;
        $this->title = $title;
        $this->sortOrder = $sortOrder;
    }

    public function getModuleName()
    {
        return $this->moduleName;
    }

    public function getAclName()
    {
        return $this->aclName;
    }

    public function getParentAcl()
    {
        return $this->parentAcl;
    }

    public function getResource()
    {
        return $this->moduleName->getName().'::'.$this->aclName;
    }

    public function getTitle()
    {
        if (is_null($this->title)) {
            return implode(' ', array_map( function($name) { return ucfirst($name); }, explode('_', $this->aclName)));
        }
        return $this->title;
    }

    public function getSortOrder()
    {
        return $this->sortOrder;
    }
}