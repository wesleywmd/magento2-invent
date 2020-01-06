<?php
namespace Wesleywmd\Invent\Model\Logger;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Logger\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'loggerName' => $input->getArgument('loggerName'),
            'fileName' => $input->getOption('fileName'),
            'type' => $input->getOption('type')
        ];
    }
}
