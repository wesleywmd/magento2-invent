<?php
namespace Wesleywmd\Invent\Model\Menu;

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
        if ($this->aclHelper->findInTree($data->getResource())) {
            return;
        }

        $aclName = explode('::', $data->getResource());
        $aclData = $this->aclDataFactory->createFromArray([
            'moduleName' => $data->getModuleName(),
            'aclName' => $aclName[1],
            'parentAcl' => $data->getParentMenu(),
            'title' => $data->getTitle(),
            'sortOrder' => $data->getSortOrder()
        ]);
        $this->aclComponent->addToModule($aclData);
    }
}