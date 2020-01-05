<?php
namespace Wesleywmd\Invent\Model\Controller;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Controller\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'controllerUrl' => $input->getArgument('controllerUrl'),
            'router' => $input->getOption('router')
        ];
    }
}
