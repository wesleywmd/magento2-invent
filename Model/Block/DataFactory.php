<?php
namespace Wesleywmd\Invent\Model\Block;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Block\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'blockName' => $input->getArgument('blockName')
        ];
    }
}
