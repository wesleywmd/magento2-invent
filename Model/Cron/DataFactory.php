<?php
namespace Wesleywmd\Invent\Model\Cron;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Cron\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'cronName' => $input->getArgument('cronName'),
            'method' => $input->getOption('method'),
            'schedule' => $input->getOption('schedule'),
            'group' => $input->getOption('group')
        ];
    }
}
