<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Menu implements ComponentInterface
{
    private $fileHelper;

    private $pathHelper;

    private $domFactory;

    private $location;

    private $aclHelper;

    private $acl;

    private $aclDataFactory;

    public function __construct(
        FileHelper $fileHelper,
        PathHelper $pathHelper,
        DomFactory $domFactory,
        Location $location,
        AclHelper $aclHelper,
        Acl $acl,
        Acl\DataFactory $aclDataFactory
    ) {
        $this->fileHelper = $fileHelper;
        $this->pathHelper = $pathHelper;
        $this->domFactory = $domFactory;
        $this->location = $location;
        $this->aclHelper = $aclHelper;
        $this->acl = $acl;
        $this->aclDataFactory = $aclDataFactory;
    }

    public function addToModule(DataInterface $data)
    {
        /** @var Menu\Data $data */
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_MENU, Location::AREA_ADMINHTML);
        $dom = $this->domFactory->create($location, Location::TYPE_MENU)
            ->updateElement('menu')
            ->updateElement('add', 'id', $data->getMenuResource(), null, ['menu'])
            ->updateAttributes([
                'title' => $data->getTitle(),
                'module' => $data->getModuleName()->getName(),
                'sortOrder' => $data->getSortOrder(),
                'resource' => $data->getResource()
            ], ['menu', 'add[@id="'.$data->getMenuResource().'"]']);
        if (!is_null($data->getParentMenu())) {
            $dom->updateAttributes([
                'parent' => $data->getParentMenu(),
                'action' => $data->getAction()
            ], ['menu', 'add[@id="'.$data->getMenuResource().'"]']);
        }
        $this->fileHelper->saveFile($location, $dom->print(), true);

        if (!$this->aclHelper->findInTree($data->getResource())) {
            $aclName = explode('::', $data->getResource());
            $aclData = $this->aclDataFactory->create([
                'moduleName' => $data->getModuleName(),
                'aclName' => $aclName[1],
                'parentAcl' => $data->getParentMenu(),
                'title' => $data->getTitle(),
                'sortOrder' => $data->getSortOrder()
            ]);
            $this->acl->addToModule($aclData);
        }
    }
}