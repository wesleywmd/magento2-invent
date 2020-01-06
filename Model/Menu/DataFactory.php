<?php
namespace Wesleywmd\Invent\Model\Menu;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Menu\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'menuName' => $input->getArgument('menuName'),
            'parentMenu' => $input->getOption('parent'),
            'title' => $input->getOption('title'),
            'sortOrder' => $input->getOption('sortOrder'),
            'action' => $input->getOption('action'),
            'resource' => $input->getOption('resource')
        ];
    }
}
