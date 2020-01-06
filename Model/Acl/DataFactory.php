<?php
namespace Wesleywmd\Invent\Model\Acl;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Acl\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'aclName' => $input->getArgument('aclName'),
            'parentAcl' => $input->getOption('parent'),
            'title' => $input->getOption('title'),
            'sortOrder' => $input->getOption('sortOrder')
        ];
    }
}
