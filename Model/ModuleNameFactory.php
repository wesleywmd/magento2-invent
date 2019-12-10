<?php
namespace Wesleywmd\Invent\Model;

use \Magento\Framework\ObjectManagerInterface;

class ModuleNameFactory
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
    public function __construct(ObjectManagerInterface $objectManager, $instanceName = '\\Wesleywmd\\Invent\\Model\\ModuleName')
    {
        $this->_objectManager = $objectManager;
        $this->_instanceName = $instanceName;
    }

    /**
     * Create class instance with specified parameters
     *
     * @param array $data
     * @return \Wesleywmd\Invent\Model\ModuleName
     */
    public function create($name)
    {
        list($vendor, $component) = $this->splitName($name);
        $data = compact('vendor', 'component');
        return $this->_objectManager->create($this->_instanceName, compact('data'));
    }

    /**
     * Splits module name into array of vendor and component strings
     *
     * @param string $name
     * @return array
     * @throws \Exception
     */
    private function splitName($name)
    {
        $pieces = explode('_', $name);
        if( count($pieces) !== 2 ) {
            throw new \Exception('Invalid module name supplied: '.$name);
        }
        return $pieces;
    }
}