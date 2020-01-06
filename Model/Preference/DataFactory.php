<?php
namespace Wesleywmd\Invent\Model\Preference;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Preference\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'for' => $input->getArgument('for'),
            'type' => $input->getArgument('type'),
            'area' => $input->getOption('area')
        ];
    }
}
