<?php
namespace Wesleywmd\Invent\Model\Crud;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\InterceptorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Acl;
use Wesleywmd\Invent\Model\Component\BaseInterceptor;

class Interceptor extends BaseInterceptor implements InterceptorInterface
{
    private $aclDataFactory;

    private $aclComponent;
    
    private $aclHelper;

    public function __construct(Acl\DataFactory $aclDataFactory, ComponentInterface $aclComponent, AclHelper $aclHelper)
    {
        $this->aclDataFactory = $aclDataFactory;
        $this->aclComponent = $aclComponent;
        $this->aclHelper = $aclHelper;
    }

    public function after(InventStyle $io, DataInterface $data)
    {
        if (is_null($data->getSectionLabel())) {
            return;
        }

        if ($this->aclHelper->findInTree($data->getSectionResource())) {
            return;
        }

        /** @var Acl\Data $data */
        $aclName = explode('::', $data->getSectionResource());
        $aclData = $this->aclDataFactory->createFromArray([
            'moduleName' => $data->getModuleName(),
            'aclName' => $aclName[1],
            'parentAcl' => 'Magento_Config::config',
            'title' => $data->getSectionLabel(),
            'sortOrder' => $data->getSectionSortOrder()
        ]);
        $this->aclComponent->addToModule($aclData);
    }
}