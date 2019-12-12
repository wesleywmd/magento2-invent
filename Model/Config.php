<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Config implements ComponentInterface
{
    private $fileHelper;

    private $pathHelper;

    private $domFactory;

    private $location;

    private $aclHelper;

    private $acl;

    private $aclDataFactory;

    public function __construct(
        FileHelper $fileHelper,
        PathHelper $pathHelper,
        DomFactory $domFactory,
        Location $location,
        AclHelper $aclHelper,
        Acl $acl,
        Acl\DataFactory $aclDataFactory
    ) {
        $this->fileHelper = $fileHelper;
        $this->pathHelper = $pathHelper;
        $this->domFactory = $domFactory;
        $this->location = $location;
        $this->aclHelper = $aclHelper;
        $this->acl = $acl;
        $this->aclDataFactory = $aclDataFactory;
    }

    public function addToModule(DataInterface $data)
    {
        /** @var Config\Data $data */
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_SYSTEM, Location::AREA_ADMINHTML);
        $sectionXpath = ['system', 'section[@id="'.$data->getSectionId().'"]'];
        $groupXpath = array_merge($sectionXpath, ['group[@id="'.$data->getGroupId().'"]']);
        $fieldXpath = array_merge($groupXpath, ['field[@id="'.$data->getFieldId().'"]']);
        $dom = $this->domFactory->create($location, Location::TYPE_SYSTEM)
            ->updateElement('system');
        if (!is_null($data->getTabLabel())) {
            $this->addTab($data, $dom);
        }
        $dom->updateElement('section', 'id', $data->getSectionId(), null, ['system']);
        if (!is_null($data->getSectionLabel())) {
            $this->addSection($data, $sectionXpath, $dom);
        }
        $dom->updateElement('group', 'id', $data->getGroupId(), null, $sectionXpath);
        if (!is_null($data->getGroupLabel())) {
            $this->addGroup($data, $groupXpath, $dom);
        }
        $dom->updateElement('field', 'id', $data->getFieldId(), null, $groupXpath)
            ->updateAttributes([
                'translate' => 'label',
                'type' => $data->getFieldType(),
                'sortOrder' => $data->getFieldSortOrder(),
                'showInDefault' => $data->getFieldShowInDefault(),
                'showInWebsite' => $data->getFieldShowInWebsite(),
                'showInStore' => $data->getFieldShowInStore()
            ], $fieldXpath)
            ->updateElement('label', null, null, $data->getFieldLabel(), $fieldXpath);
        if (!is_null($data->getFieldComment())) {
            $dom->updateElement('comment', null, null, $data->getFieldComment(), $fieldXpath);
        }
        $this->fileHelper->saveFile($location, $dom->print(), true);
    }

    private function addTab(Config\Data $data, &$dom)
    {
        $dom->updateElement('tab', 'id', $data->getTabId(), null, ['system'])
            ->updateAttributes([
                'translate' => 'label',
                'sortOrder' => $data->getTabSortOrder()
            ], ['system', 'tab[@id="'.$data->getTabId().'"]'])
            ->updateElement('label', null, null, $data->getTabLabel(), ['system', 'tab[@id="'.$data->getTabId().'"]']);
    }

    private function addSection(Config\Data $data, $sectionXpath, &$dom)
    {
        $dom->updateAttributes([
            'translate' => 'label',
            'sortOrder' => $data->getSectionSortOrder(),
            'showInDefault' => $data->getSectionShowInDefault(),
            'showInWebsite' => $data->getSectionShowInWebsite(),
            'showInStore' => $data->getSectionShowInStore()
        ], $sectionXpath)
            ->updateElement('class', null, null, $data->getSectionClass(), $sectionXpath)
            ->updateElement('label', null, null, $data->getSectionLabel(), $sectionXpath)
            ->updateElement('tab', null, null, $data->getSectionTab(), $sectionXpath)
            ->updateElement('resource', null, null, $data->getSectionResource(), $sectionXpath);
        if (!$this->aclHelper->findInTree($data->getSectionResource())) {
            $this->createSectionResourceAcl($data);
        }
    }

    private function addGroup(Config\Data $data, $groupXpath, &$dom)
    {
        $dom->updateAttributes([
            'translate' => 'label',
            'sortOrder' => $data->getGroupSortOrder(),
            'showInDefault' => $data->getGroupShowInDefault(),
            'showInWebsite' => $data->getGroupShowInWebsite(),
            'showInStore' => $data->getGroupShowInStore()
        ], $groupXpath)
            ->updateElement('label', null, null, $data->getGroupLabel(), $groupXpath);

    }

    private function createSectionResourceAcl(Config\Data $data)
    {
        $aclName = explode('::', $data->getSectionResource());
        $aclData = $this->aclDataFactory->create([
            'moduleName' => $data->getModuleName(),
            'aclName' => $aclName[1],
            'parentAcl' => 'Magento_Config::config',
            'title' => $data->getSectionLabel(),
            'sortOrder' => $data->getSectionSortOrder()
        ]);
        $this->acl->addToModule($aclData);
    }
}