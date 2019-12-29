<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements XmlRendererInterface
{
    protected function getType()
    {
        return Location::TYPE_DB_SCHEMA;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $dom->updateElement('table', 'name', $data->getTable())
            ->updateAttributes([
                'resource' => 'default',
                'engine' => 'innodb',
                'comment' => str_replace('\\', ' ', $data->getModuleName()->getNamespace([$data->getModelName()]))
            ], ['table[@name="'.$data->getTable().'"]']);

        if (!$data->getNoEntityId()) {
            $dom->updateElement('column', 'name', 'entity_id', null, ['table[@name="'.$data->getTable().'"]'])
                ->updateAttributes([
                    'xsi:type' => 'smallint',
                    'padding' => '6',
                    'unsigned' => 'false',
                    'nullable' => 'false',
                    'identity' => 'true',
                    'comment' => 'Entity ID'
                ], ['table[@name="'.$data->getTable().'"]', 'column[@name="entity_id"]']);
        }

        foreach ($data->getColumns() as $column) {
            $dom->updateElement('column', 'name', $column, null, ['table[@name="'.$data->getTable().'"]'])
                ->updateAttributes([
                    'xsi:type' => 'varchar',
                    'length' => '32',
                    'nullable' => 'false',
                    'comment' => implode(' ', array_map( function($piece) { return ucfirst($piece); }, explode('_', $column))),
                ], ['table[@name="'.$data->getTable().'"]', 'column[@name="'.$column.'"]']);
        }

        if (!$data->getNoCreatedAt()) {
            $dom->updateElement('column', 'name', 'created_at', null, ['table[@name="'.$data->getTable().'"]'])
                ->updateAttributes([
                    'xsi:type' => 'timestamp',
                    'on_update' => 'false',
                    'nullable' => 'false',
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => 'Created At'
                ], ['table[@name="'.$data->getTable().'"]', 'column[@name="created_at"]']);
        }

        if (!$data->getNoUpdatedAt()) {
            $dom->updateElement('column', 'name', 'updated_at', null, ['table[@name="'.$data->getTable().'"]'])
                ->updateAttributes([
                    'xsi:type' => 'timestamp',
                    'on_update' => 'true',
                    'nullable' => 'false',
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => 'Updated At'
                ], ['table[@name="'.$data->getTable().'"]', 'column[@name="updated_at"]']);
        }

        if (!$data->getNoEntityId()) {
            $dom->updateElement('constraint', 'referenceId', 'PRIMARY', null, ['table[@name="' . $data->getTable() . '"]'])
                ->updateAttribute('xsi:type', 'primary', ['table[@name="' . $data->getTable() . '"]', 'constraint[@referenceId="PRIMARY"]'])
                ->updateElement('column', 'name', 'entity_id', null, ['table[@name="' . $data->getTable() . '"]', 'constraint[@referenceId="PRIMARY"]']);
        }
    }
}