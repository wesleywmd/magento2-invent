<?php
namespace Wesleywmd\Invent\Model\Component;

use Magento\Framework\ObjectManagerInterface;
use Symfony\Component\Console\Input\InputInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class BaseDataFactory implements DataFactoryInterface
{
    protected $objectManager;

    protected $moduleNameFactory;

    protected $instanceName;

    public function __construct(
        ObjectManagerInterface $objectManager,
        ModuleNameFactory $moduleNameFactory
    ) {
        $this->objectManager = $objectManager;
        $this->moduleNameFactory = $moduleNameFactory;
    }

    public function create(InputInterface $input)
    {
        return $this->objectManager->create($this->instanceName, $this->getData($input));
    }

    public function createFromArray($dataArray)
    {
        return $this->objectManager->create($this->instanceName, $dataArray);
    }

    protected function getDataArray(InputInterface $input)
    {
        return [];
    }

    private function getData(InputInterface $input)
    {
        return array_merge([
            'moduleName' => $this->moduleNameFactory->create($input->getArgument('moduleName'))
        ], $this->getDataArray($input));
    }
}
