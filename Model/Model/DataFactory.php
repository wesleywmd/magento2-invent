<?php
namespace Wesleywmd\Invent\Model\Model;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Model\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'modelName' => $input->getArgument('modelName'),
            'columns' => $input->getOption('column'),
            'tableName' => $input->getOption('tableName'),
            'noEntityId' => $input->getOption('no-entity-id'),
            'noCreatedAt' => $input->getOption('no-created-at'),
            'noUpdatedAt' => $input->getOption('no-updated-at')
        ];
    }
}
