<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class UiListingXml extends AbstractXmlRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getUiListingPath();
    }

    protected function getType()
    {
        return Location::TYPE_LISTING;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Crud\Data $data */

        $this->addArgument($dom,
            $data->getUiListingDataSource(true),
            $data->getUiListingColumns(),
            $data->getModel()->getModelName()
        );
        $this->addDataSource($dom, $data->getUiListingDataSource());
        $this->addListingToolbar($dom);
        $this->addColumns($dom,
            $data->getUiListingColumns(),
            $data->getUiListingSelectProvider(),
            $data->getActionsColumnNamespace(),
            $data->getUiListingColumnsEditor(),
            $data->getModel()->getColumns()
        );
    }

    private function addArgument(Dom &$dom, $dataSource, $columns, $name)
    {
        $xpath = $dom->node('argument', ['name'=>'data', 'xsi:type'=>'array']);
        $xpathJsConfig = $dom->node('item', ['name'=>'js_config', 'xsi:type'=>'array'], null, $xpath);
        $dom->node('item', ['name'=>'provider', 'xsi:type'=>'string'], $dataSource, $xpathJsConfig);
        $dom->node('item', ['name'=>'deps', 'xsi:type'=>'string'], $dataSource, $xpathJsConfig);
        $dom->node('item', ['name'=>'spinner', 'xsi:type'=>'string'], $columns, $xpath);
        $xpathButtons = $dom->node('item', ['name'=>'buttons', 'xsi:type'=>'array'], null, $xpath);
        $xpathButtonsAdd = $dom->node('item', ['name'=>'add', 'xsi:type'=>'array'], null, $xpathButtons);
        $dom->node('item', ['name'=>'name', 'xsi:type'=>'string'], 'add', $xpathButtonsAdd);
        $dom->node('item', ['name'=>'label', 'xsi:type'=>'string', 'translate'=>'true'], 'Add New '. $name, $xpathButtonsAdd);
        $dom->node('item', ['name'=>'class', 'xsi:type'=>'string'], 'primary', $xpathButtonsAdd);
        $dom->node('item', ['name'=>'url', 'xsi:type'=>'string'], '*/*/create', $xpathButtonsAdd);
    }

    private function addDataSource(Dom &$dom, $dataSource)
    {
        $xpath = $dom->node('dataSource', ['name'=>$dataSource]);
        $xpathDataProvider = $dom->node('argument', ['name'=>'dataProvider', 'xsi:type'=>'configurableObject'], null, $xpath);
        $dom->node('argument', ['name'=>'class', 'xsi:type'=>'string'], 'Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider', $xpathDataProvider);
        $dom->node('argument', ['name'=>'name', 'xsi:type'=>'string'], $dataSource, $xpathDataProvider);
        $dom->node('argument', ['name'=>'primaryFieldName', 'xsi:type'=>'string'], 'entity_id', $xpathDataProvider);
        $dom->node('argument', ['name'=>'requestFieldName', 'xsi:type'=>'string'], 'id', $xpathDataProvider);
        $xpathData = $dom->node('argument', ['name'=>'data', 'xsi:type'=>'array'] , null, $xpathDataProvider);
        $xpathConfig = $dom->node('item', ['name'=>'config', 'xsi:type'=>'array'], null, $xpathData);
        $dom->node('item', ['name'=>'component', 'xsi:type'=>'string'], 'Magento_Ui/js/grid/provider', $xpathConfig);
        $dom->node('item', ['name'=>'update_url', 'xsi:type'=>'url', 'path'=>'mui/index/render'], null, $xpathConfig);
        $xpathStorageConfig = $dom->node('item', ['name'=>'storageConfig', 'xsi:type'=>'array'], null, $xpathConfig);
        $dom->node('item', ['name'=>'indexField', 'xsi:type'=>'string'], 'entity_id', $xpathStorageConfig);
    }

    private function addListingToolbar(Dom &$dom)
    {
        $xpath = $dom->node('listingToolbar', ['name'=>'listing_top']);
        $xpathArgument = $dom->node('argument', ['name'=>'data', 'xsi:type'=>'array'], null, $xpath);
        $xpathConfig = $dom->node('item', ['name'=>'config', 'xsi:type'=>'array'], null, $xpathArgument);
        $dom->node('item', ['name'=>'sticky', 'xsi:type'=>'boolean'], 'true', $xpathConfig);
        $dom->node('bookmark', ['name'=>'bookmarks'], null, $xpath);
        $dom->node('filterSearch', ['name'=>'fulltext'], null, $xpath);
        $dom->node('filters', ['name'=>'listing_filters'], null, $xpath);
        $xpathMassaction = $dom->node('massaction', ['name'=>'listing_massaction'], null, $xpath);
        $xpathAction = $dom->node('action', ['name'=>'delete'], null, $xpathMassaction);
        $xpathSettings = $dom->node('settings', [], null, $xpathAction);
        $xpathConfirm = $dom->node('confirm', [], null, $xpathSettings);
        $dom->node('message', ['translate'=>'true'], 'Are you sure you want to delete selected items?', $xpathConfirm);
        $dom->node('title', ['translate'=>'true'], 'Delete items', $xpathConfirm);
        $dom->node('url', ['path'=>'*/*/massDelete'], null, $xpathSettings);
        $dom->node('type', [], 'delete', $xpathSettings);
        $dom->node('label', ['translate'=>'true'], 'Delete', $xpathSettings);
        $dom->node('paging', ['name'=>'listing_paging'], null, $xpath);
    }

    private function addColumns(Dom &$dom, $uiColumns, $uiProvider, $actionsColumn, $uiEditor, $columns)
    {
        $xpath = $dom->node('columns', ['name'=>$uiColumns]);
        $xpathSettings = $dom->node('settings', [], null, $xpath);
        $xpathEditorConfig = $dom->node('editorConfig', [], null, $xpathSettings);
        $xpathClientConfig = $dom->node('param', ['name'=>'clientConfig', 'xsi:type'=>'array'], null, $xpathEditorConfig);
        $dom->node('item', ['name'=>'saveUrl', 'xsi:type'=>'url', 'path'=>'*/*/inlineEdit'], null, $xpathClientConfig);
        $dom->node('item', ['name'=>'validateBeforeSave', 'xsi:type'=>'boolean'], 'false', $xpathClientConfig);
        $dom->node('param', ['name'=>'indexField', 'xsi:type'=>'string'], 'entity_id', $xpathEditorConfig);
        $dom->node('param', ['name'=>'enabled', 'xsi:type'=>'boolean'], 'true', $xpathEditorConfig);
        $dom->node('param', ['name'=>'selectProvider', 'xsi:type'=>'string'], $uiProvider, $xpathEditorConfig);
        $xpathChildDefaults = $dom->node('childDefaults', [], null, $xpathSettings);
        $xpathFieldAction = $dom->node('param', ['name'=>'fieldAction', 'xsi:type'=>'array'], null, $xpathChildDefaults);
        $dom->node('item', ['name'=>'provider', 'xsi:type'=>'string'], $uiEditor, $xpathFieldAction);
        $dom->node('item', ['name'=>'target', 'xsi:type'=>'string'], 'startEdit', $xpathFieldAction);
        $xpathFieldParams = $dom->node('item', ['name'=>'params', 'xsi:type'=>'array'], null, $xpathFieldAction);
        $dom->node('item', ['name'=>'0', 'xsi:type'=>'string'], '${ $.$data.rowIndex }', $xpathFieldParams);
        $dom->node('item', ['name'=>'1', 'xsi:type'=>'boolean'], 'true', $xpathFieldParams);
        $xpathSelections = $dom->node('selectionsColumn', ['name'=>'ids'], null, $xpath);
        $xpathSettings = $dom->node('settings', [], null, $xpathSelections);
        $dom->node('indexField', [], 'entity_id', $xpathSettings);
        $dom->node('resizeDefaultWidth', [], '55', $xpathSettings);
        $dom->node('resizeEnabled', [], 'false', $xpathSettings);
        $xpathEntityId = $dom->node('column', ['name'=>'entity_id'], null, $xpath);
        $xpathSettings = $dom->node('settings', [], null, $xpathEntityId);
        $dom->node('filter', [], 'textRange', $xpathSettings);
        $dom->node('sorting', [], 'asc', $xpathSettings);
        $dom->node('label', ['translate'=>'true'], 'ID', $xpathSettings);
        foreach ($columns as $column) {
            $xpathColumn = $dom->node('column', ['name'=>$column], null, $xpath);
            $xpathSettings = $dom->node('settings', [], null, $xpathColumn);
            $dom->node('filter', [], 'text', $xpathSettings);
            $xpathEditor = $dom->node('editor', [], null, $xpathSettings);
            $xpathValidation = $dom->node('validation', [], null, $xpathEditor);
            $dom->node('rule', ['name'=>'required-entry', 'xsi:type'=>'boolean'], 'true', $xpathValidation);
            $dom->node('editorType', [], 'text', $xpathEditor);
            $dom->node('label', ['translate'=>'true'], ucwords(str_replace('_', '-', $column)), $xpathSettings);
        }
        $xpathActions = $dom->node('actionsColumn', ['name'=>'actions', 'class'=>$actionsColumn], null, $xpath);
        $xpathSettings = $dom->node('settings', [], null, $xpathActions);
        $dom->node('indexField', [], 'entity_id', $xpathSettings);
        $dom->node('resizeEnabled', [], 'false', $xpathSettings);
        $dom->node('resizeDefaultWidth', [], 107, $xpathSettings);
    }
}