<?php
namespace Wesleywmd\Invent\Model\Config;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements XmlRendererInterface
{
    protected function getType()
    {
        return Location::TYPE_SYSTEM;
    }
    
    protected function getArea(DataInterface $data)
    {
        return Location::AREA_ADMINHTML;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $dom->updateElement('system');

        if (!is_null($data->getTabLabel())) {
            $this->addTab($data, $dom);
        }
        
        $sectionXpath = $this->addSectionNode($dom, $data);
        $groupXpath = $this->addGroup($dom, $data, $sectionXpath);
        $fieldXpath = $this->addFieldNode($dom, $data, $groupXpath);
    }

    private function addTabNode(Dom &$dom, DataInterface $data)
    {
        $tabXpath = ['system', 'tab[@id="'.$data->getTabId().'"]'];
        $dom->updateElement('tab', 'id', $data->getTabId(), null, ['system'])
            ->updateAttributes([
                'translate' => 'label',
                'sortOrder' => $data->getTabSortOrder()
            ], $tabXpath)
            ->updateElement('label', null, null, $data->getTabLabel(), $tabXpath);
        return $tabXpath;
    }

    private function addSectionNode(Dom &$dom, DataInterface $data)
    {
        $sectionXpath = ['system', 'section[@id="'.$data->getSectionId().'"]'];
        $dom->updateElement('section', 'id', $data->getSectionId(), null, ['system']);
        if (!is_null($data->getSectionLabel())) {
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
        }
        return $sectionXpath;
    }

    private function addGroup(Dom &$dom, DataInterface $data, $sectionXpath)
    {
        $groupXpath = array_merge($sectionXpath, ['group[@id="'.$data->getGroupId().'"]']);
        $dom->updateElement('group', 'id', $data->getGroupId(), null, $sectionXpath);
        if (!is_null($data->getGroupLabel())) {
            $dom->updateAttributes([
                'translate' => 'label',
                'sortOrder' => $data->getGroupSortOrder(),
                'showInDefault' => $data->getGroupShowInDefault(),
                'showInWebsite' => $data->getGroupShowInWebsite(),
                'showInStore' => $data->getGroupShowInStore()
            ], $groupXpath)
                ->updateElement('label', null, null, $data->getGroupLabel(), $groupXpath);
        }
        return $groupXpath;
    }
    
    private function addFieldNode(Dom &$dom, DataInterface $data, $groupXpath)
    {
        $fieldXpath = array_merge($groupXpath, ['field[@id="'.$data->getFieldId().'"]']);
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
        return $fieldXpath;
    }
}