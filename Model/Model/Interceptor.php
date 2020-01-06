<?php
namespace Wesleywmd\Invent\Model\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\InterceptorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Component\BaseInterceptor;
use Wesleywmd\Invent\Model\Preference;
use Wesleywmd\Invent\Model\ModuleName;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Interceptor extends BaseInterceptor implements InterceptorInterface
{
    private $preferenceDataFactory;

    private $preferenceComponent;

    public function __construct(Preference\DataFactory $preferenceDataFactory, ComponentInterface $preferenceComponent)
    {
        $this->preferenceDataFactory = $preferenceDataFactory;
        $this->preferenceComponent = $preferenceComponent;
    }

    public function after(InventStyle $io, DataInterface $data)
    {
        $this->createPreference($data->getModuleName(), $data->getInterfaceInstance(), $data->getInstance());
        $this->createPreference($data->getModuleName(), $data->getSearchResultsInterfaceInstance(), 'Magento\Framework\Api\SearchResults');
        $this->createPreference($data->getModuleName(), $data->getRepositoryInterfaceInstance(), $data->getRepositoryInstance());
    }

    private function createPreference(ModuleName $moduleName, $for, $type)
    {
        $preferenceData = $this->preferenceDataFactory->createFromArray([
            'moduleName' => $moduleName,
            'for' => $for,
            'type' => $type,
            'area' => Location::AREA_GLOBAL
        ]);
        $this->preferenceComponent->addToModule($preferenceData);
    }
}