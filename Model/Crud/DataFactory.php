<?php
namespace Wesleywmd\Invent\Model\Crud;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Crud\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'modelName' => $input->getArgument('modelName'),
            'noModel' => $input->getOption('no-model')
        ];
    }
}
