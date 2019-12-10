<?php
namespace Wesleywmd\Invent\Model;

class ModuleName
{
    private $vendor;

    private $component;

    public function __construct($data = [])
    {
        $this->vendor = $data['vendor'];
        $this->component = $data['component'];
    }

    public function getVendor()
    {
        return $this->vendor;
    }

    public function getComponent()
    {
        return $this->component;
    }

    public function getName($extras = [])
    {
        return $this->getUcString('_', $extras);
    }
    
    public function getNamespace($extras = [])
    {
        return $this->getUcString('\\', $extras);
    }

    public function getLocation($extras = [])
    {
        return $this->getUcString(DIRECTORY_SEPARATOR, $extras);
    }
    
    public function getSlug($extras = [])
    {
        $slug = strtolower($this->getName());
        foreach( $extras as $extra ) {
            $slug .= '_' . strtolower($extra);
        }
        return $slug;
    }

    protected function getUcString($separator, $extras = [])
    {
        $string = ucfirst($this->vendor) . $separator . ucfirst($this->component);
        foreach( $extras as $extra ) {
            $string .= $separator . ucfirst($extra);
        }
        return $string;
    }
}