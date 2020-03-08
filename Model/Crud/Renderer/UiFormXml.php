<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class UiFormXml extends AbstractXmlRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getUiFormPath();
    }

    protected function getType()
    {
        return Location::TYPE_LISTING;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Crud\Data $data */
        $this->addArgumentNode($dom, $data);
        $this->addSettingsNode($dom, $data);
        $this->addDataSourceNode($dom, $data);
        $this->addFieldsetNode($dom, $data);
    }

    private function addArgumentNode(Dom $dom, Crud\Data $data)
    {
        $xpath = $dom->node('argument', ['name'=>'data', 'xsi:type'=>'array']);
        $xpathJsConfig = $dom->node('item', ['name'=>'js_config', 'xsi:type'=>'array'], null, $xpath);
        $dom->node('item', ['name'=>'provider', 'xsi:type'=>'string'], $data->getUiFormDataSource(true), $xpathJsConfig);
        $dom->node('item', ['name'=>'deps', 'xsi:type'=>'string'], $data->getUiFormDataSource(true), $xpathJsConfig);
        $dom->node('item', ['name'=>'template', 'xsi:type'=>'string'],'templates/form/collapsible', $xpath);
    }

    private function addSettingsNode(Dom $dom, Crud\Data $data)
    {
        $xpath = $dom->node('settings');
        $dom->node('namespace', [], $data->getUiFormName(), $xpath);
        $dom->node('dataScope', [], 'data', $xpath);
        $xpathButtons = $dom->node('buttons', [], null, $xpath);
        $xpathBack = $dom->node('button', ['name'=>'back'], null, $xpathButtons);
        $dom->node('url', ['path'=>'*/*/index'], null, $xpathBack);
        $dom->node('class', [], 'back', $xpathBack);
        $dom->node('label', ['translate'=>'true'], 'Back', $xpathBack);
        $xpathReset = $dom->node('button', ['name'=>'reset'], null, $xpathButtons);
        $dom->node('class', [], 'reset', $xpathReset);
        $dom->node('label', ['translate'=>'true'], 'Reset', $xpathReset);
        $dom->node('button', ['name'=>'save', 'class'=>$data->getUiFormSaveButtonClass()], null, $xpathButtons);
    }

    private function addDataSourceNode(Dom $dom, Crud\Data $data)
    {
        $xpath = $dom->node('dataSource', ['name'=>$data->getUiFormDataSource()]);
        $xpathArgument = $dom->node('argument', ['name'=>'data', 'xsi:type'=>'array'], null, $xpath);
        $xpathJsConfig = $dom->node('item', ['name'=>'js_config', 'xsi:type'=>'array'], null, $xpathArgument);
        $dom->node('item', ['name'=>'component', 'xsi:type'=>'string'], 'Magento_Ui/js/form/provider', $xpathJsConfig);
        $dom->node('item', ['name'=>'submit_url', 'xsi:type'=>'url'], '*/*/save', $xpathJsConfig);
        $xpathDataProvider = $dom->node('dataProvider', ['class'=>$data->getUiFormDataProviderClass(), 'name'=>$data->getUiFormDataSource()], null, $xpath);
        $xpathSettings = $dom->node('settings', [], null, $xpathDataProvider);
        $dom->node('primaryFieldName', [], 'entity_id', $xpathSettings);
        $dom->node('requestFieldName', [], 'entity_id', $xpathSettings);
    }

    private function addFieldsetNode(Dom $dom, Crud\Data $data)
    {
        $xpath = $dom->node('fieldset', ['name'=>'general']);
        $xpathSettings = $dom->node('settings', [], null, $xpath);
        $dom->node('label', [], null, $xpathSettings);
        $sortOrder = 10;
        foreach ($data->getModel()->getColumns() as $column) {
            $xpathField = $dom->node('field', ['name'=>'label', 'formElement', 'sortOrder'=>$sortOrder], $xpath);
            $xpathSettings = $dom->node('settings', [], null, $xpathField);
            $xpathValidation = $dom->node('validation', [], null, $xpathSettings);
            $dom->node('rule', ['name'=>'required-entry', 'xsi:type'=>'boolean'], 'true', $xpathValidation);
            $dom->node('dataType', [], 'text', $xpathSettings);
            $dom->node('label', ['translate'=>'true'], ucwords(str_replace('_', ' ', $column)), $xpathSettings);
            $sortOrder += 10;
        }
    }
}