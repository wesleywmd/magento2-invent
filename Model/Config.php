<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\Component\AbstractComponent;

class Config extends AbstractComponent implements ComponentInterface
{
    private $aclHelper;

    private $acl;

    private $aclDataFactory;

    public function __construct(
        FileHelper $fileHelper,
        Config\XmlRenderer $xmlRenderer,
        AclHelper $aclHelper,
        Acl $acl,
        Acl\DataFactory $aclDataFactory
    ) {
        parent::__construct($fileHelper, null, $xmlRenderer);
        $this->aclHelper = $aclHelper;
        $this->acl = $acl;
        $this->aclDataFactory = $aclDataFactory;
    }

    public function addToModule(DataInterface $data)
    {
        /** @var Config\Data $data */
        $this->createXmlFile($data);
        if (!is_null($data->getSectionLabel())) {
            $this->createSectionResourceAcl($data);
        }
    }

    private function createSectionResourceAcl(DataInterface $data)
    {
        /** @var Config\Data $data */
        $aclName = explode('::', $data->getSectionResource());
        $aclData = $this->aclDataFactory->create([
            'moduleName' => $data->getModuleName(),
            'aclName' => $aclName[1],
            'parentAcl' => 'Magento_Config::config',
            'title' => $data->getSectionLabel(),
            'sortOrder' => $data->getSectionSortOrder()
        ]);
        $this->acl->addToModule($aclData);
    }
}