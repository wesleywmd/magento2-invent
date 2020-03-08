<?php
namespace Wesleywmd\Invent\Model\XmlParser;

use Magento\Framework\ObjectManagerInterface;

class DomFactory
{
    /**
     * Object Manager instance
     *
     * @var ObjectManagerInterface
     */
    protected $_objectManager = null;

    /**
     * Instance name to create
     *
     * @var string
     */
    protected $_instanceName = null;

    /**
     * Factory constructor
     *
     * @param ObjectManagerInterface $objectManager
     * @param string $instanceName
     */
    public function __construct(ObjectManagerInterface $objectManager, $instanceName = '\\Wesleywmd\\Invent\\Model\\XmlParser\\Dom')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param ModuleName $moduleName
     * @param string $type
     * @param string $area
     * @return Dom
     */
    public function create($location, $type)
    {
        $parentNode = $this->getXmlNode($type);
        if (is_file($location)) {
            $dom = simplexml_load_file($location);
        } else {
            $xsd = $this->getXsdLoction($type);
            $xml = '<'.$parentNode.' xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:noNamespaceSchemaLocation="'.$xsd.'"></'.$parentNode.'>';
            $dom = new \SimpleXMLElement($xml);
        }
        return $this->_objectManager->create($this->_instanceName, compact('dom', 'parentNode'));
    }

    private function getXsdLoction($type)
    {
        $xsdLocations = [
            Location::TYPE_MODULE    => 'urn:magento:framework:Module/etc/module.xsd',
            Location::TYPE_DI        => 'urn:magento:framework:ObjectManager/etc/config.xsd',
            Location::TYPE_CRONTAB   => 'urn:magento:module:Magento_Cron:etc/crontab.xsd',
            Location::TYPE_ROUTE     => 'urn:magento:framework:App/etc/routes.xsd',
            Location::TYPE_DB_SCHEMA => 'urn:magento:framework:Setup/Declaration/Schema/etc/schema.xsd',
            Location::TYPE_ACL       => 'urn:magento:framework:Acl/etc/acl.xsd',
            Location::TYPE_MENU      => 'urn:magento:module:Magento_Backend:etc/menu.xsd',
            Location::TYPE_SYSTEM    => 'urn:magento:module:Magento_Config:etc/system_file.xsd',
            Location::TYPE_LAYOUT    => 'urn:magento:framework:View/Layout/etc/page_configuration.xsd',
            Location::TYPE_LISTING   => 'urn:magento:module:Magento_Ui:etc/ui_configuration.xsd',
            Location::TYPE_FORM      => 'urn:magento:module:Magento_Ui:etc/ui_configuration.xsd'
        ];
        return $xsdLocations[$type];
    }

    private function getXmlNode($type)
    {
        $xmlNode = [
            Location::TYPE_MODULE    => 'config',
            Location::TYPE_DI        => 'config',
            Location::TYPE_CRONTAB   => 'config',
            Location::TYPE_ROUTE     => 'config',
            Location::TYPE_ACL       => 'config',
            Location::TYPE_MENU      => 'config',
            Location::TYPE_SYSTEM    => 'config',
            Location::TYPE_DB_SCHEMA => 'schema',
            Location::TYPE_LAYOUT    => 'page',
            Location::TYPE_LISTING   => 'listing',
            Location::TYPE_FORM      => 'form'
        ];
        return $xmlNode[$type];
    }
}
