<?php
namespace Wesleywmd\Invent\Model\Config;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $sectionId;

    private $groupId;

    private $fieldId;

    private $tabId;

    private $tabLabel;

    private $tabSortOrder;

    private $sectionLabel;

    private $sectionSortOrder;

    private $sectionShowInDefault;

    private $sectionShowInWebsite;

    private $sectionShowInStore;

    private $sectionClass;

    private $sectionTab;

    private $sectionResource;

    private $groupLabel;

    private $groupSortOrder;

    private $groupShowInDefault;

    private $groupShowInWebsite;

    private $groupShowInStore;

    private $fieldType;

    private $fieldLabel;

    private $fieldComment;

    private $fieldSortOrder;

    private $fieldShowInDefault;

    private $fieldShowInWebsite;

    private $fieldShowInStore;

    public function __construct(
        ModuleName $moduleName,
        $configName,
        $tabId = null,
        $tabLabel = null,
        $tabSortOrder = 10,
        $sectionLabel = null,
        $sectionSortOrder = 10,
        $sectionShowInDefault = 1,
        $sectionShowInWebsite = 1,
        $sectionShowInStore = 1,
        $sectionClass = 'separator-top',
        $sectionTab = null,
        $sectionResource = null,
        $groupLabel = null,
        $groupSortOrder = 10,
        $groupShowInDefault = 1,
        $groupShowInWebsite = 1,
        $groupShowInStore = 1,
        $fieldLabel = null,
        $fieldType = 'text',
        $fieldSortOrder = 10,
        $fieldShowInDefault = 1,
        $fieldShowInWebsite = 1,
        $fieldShowInStore = 1,
        $fieldComment = 1
    ) {
        parent::__construct($moduleName, '', []);
        list($this->sectionId, $this->groupId, $this->fieldId) = explode('/', $configName);
        $this->tabId = $tabId;
        $this->tabLabel = $tabLabel;
        $this->tabSortOrder = $tabSortOrder;
        $this->sectionLabel = $sectionLabel;
        $this->sectionSortOrder = $sectionSortOrder;
        $this->sectionShowInDefault = $sectionShowInDefault;
        $this->sectionShowInWebsite = $sectionShowInWebsite;
        $this->sectionShowInStore = $sectionShowInStore;
        $this->sectionClass = $sectionClass;
        $this->sectionTab = $sectionTab;
        $this->sectionResource = $sectionResource;
        $this->groupLabel = $groupLabel;
        $this->groupSortOrder = $groupSortOrder;
        $this->groupShowInDefault = $groupShowInDefault;
        $this->groupShowInWebsite = $groupShowInWebsite;
        $this->groupShowInStore = $groupShowInStore;
        $this->fieldLabel = $fieldLabel;
        $this->fieldType = $fieldType;
        $this->fieldSortOrder = $fieldSortOrder;
        $this->fieldShowInDefault = $fieldShowInDefault;
        $this->fieldShowInWebsite = $fieldShowInWebsite;
        $this->fieldShowInStore = $fieldShowInStore;
        $this->fieldComment = $fieldComment;
    }

    public function getSectionId()
    {
        return $this->sectionId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function getFieldId()
    {
        return $this->fieldId;
    }

    public function getTabId()
    {
        if (is_null($this->tabId)) {
            return str_replace(' ', '_', strtolower($this->tabLabel));
        }
        return $this->tabId;
    }

    public function getTabLabel()
    {
        return $this->tabLabel;
    }

    public function getTabSortOrder()
    {
        return $this->tabSortOrder;
    }

    public function getSectionLabel()
    {
        return $this->sectionLabel;
    }

    public function getSectionSortOrder()
    {
        return $this->sectionSortOrder;
    }

    public function getSectionShowInDefault()
    {
        return $this->sectionShowInDefault;
    }

    public function getSectionShowInWebsite()
    {
        return $this->sectionShowInWebsite;
    }

    public function getSectionShowInStore()
    {
        return $this->sectionShowInStore;
    }

    public function getSectionClass()
    {
        return $this->sectionClass;
    }

    public function getSectionTab()
    {
        if (is_null($this->sectionTab)) {
            if (is_null($this->tabLabel)) {
                return 'service';
            }
            return $this->getTabId();
        }
        return $this->sectionTab;
    }

    public function getSectionResource()
    {
        if (is_null($this->sectionResource)) {
            return $this->moduleName->getName().'::'.$this->sectionId.'_config';
        }
        return $this->sectionResource;
    }

    public function getGroupLabel()
    {
        return $this->groupLabel;
    }

    public function getGroupSortOrder()
    {
        return $this->groupSortOrder;
    }

    public function getGroupShowInDefault()
    {
        return $this->groupShowInDefault;
    }

    public function getGroupShowInWebsite()
    {
        return $this->groupShowInWebsite;
    }

    public function getGroupShowInStore()
    {
        return $this->groupShowInStore;
    }

    public function getFieldType()
    {
        return $this->fieldType;
    }

    public function getFieldLabel()
    {
        if (is_null($this->fieldLabel)) {
            return ucwords(str_replace('_', ' ', $this->fieldId));
        }
        return $this->fieldLabel;
    }

    public function getFieldComment()
    {
        return $this->fieldComment;
    }

    public function getFieldSortOrder()
    {
        return $this->fieldSortOrder;
    }

    public function getFieldShowInDefault()
    {
        return $this->fieldShowInDefault;
    }

    public function getFieldShowInWebsite()
    {
        return $this->fieldShowInWebsite;
    }

    public function getFieldShowInStore()
    {
        return $this->fieldShowInStore;
    }
}