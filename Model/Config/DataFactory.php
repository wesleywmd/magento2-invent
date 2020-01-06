<?php
namespace Wesleywmd\Invent\Model\Config;

use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\Component\BaseDataFactory;

class DataFactory extends BaseDataFactory implements DataFactoryInterface
{
    protected $instanceName = '\Wesleywmd\Invent\Model\Config\Data';

    protected function getDataArray(InputInterface $input)
    {
        return [
            'configName' => $input->getArgument('configName'),
            'tabId' => $input->getOption('tabId'),
            'tabLabel' => $input->getOption('tabLabel'),
            'tabSortOrder' => $input->getOption('tabSortOrder'),
            'sectionLabel' => $input->getOption('sectionLabel'),
            'sectionSortOrder' => $input->getOption('sectionSortOrder'),
            'sectionShowInDefault' => $input->getOption('sectionShowInDefault'),
            'sectionShowInWebsite' => $input->getOption('sectionShowInWebsite'),
            'sectionShowInStore' => $input->getOption('sectionShowInStore'),
            'sectionClass' => $input->getOption('sectionClass'),
            'sectionTab' => $input->getOption('sectionTab'),
            'sectionResource' => $input->getOption('sectionResource'),
            'groupLabel' => $input->getOption('groupLabel'),
            'groupSortOrder' => $input->getOption('groupSortOrder'),
            'groupShowInDefault' => $input->getOption('groupShowInDefault'),
            'groupShowInWebsite' => $input->getOption('groupShowInWebsite'),
            'groupShowInStore' => $input->getOption('groupShowInStore'),
            'fieldLabel' => $input->getOption('fieldLabel'),
            'fieldType' => $input->getOption('fieldType'),
            'fieldSortOrder' => $input->getOption('fieldSortOrder'),
            'fieldShowInDefault' => $input->getOption('fieldShowInDefault'),
            'fieldShowInWebsite' => $input->getOption('fieldShowInWebsite'),
            'fieldShowInStore' => $input->getOption('fieldShowInStore'),
            'fieldComment' => $input->getOption('fieldComment')
        ];
    }
}
