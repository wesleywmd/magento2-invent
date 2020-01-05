<?php
namespace Wesleywmd\Invent\Model\Command;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Command\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'commandName' => $input->getArgument('commandName')
        ];
    }
}
