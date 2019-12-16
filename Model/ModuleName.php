<?php
namespace Wesleywmd\Invent\Model;

use Magento\Framework\App\Filesystem\DirectoryList;

class ModuleName
{
    private $vendor;

    private $component;

    private $directoryList;

    public function __construct(DirectoryList $directoryList, $vendor, $component)
    {
        $this->directoryList = $directoryList;
        $this->vendor = ucfirst($vendor);
        $this->component = ucfirst($component);
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
        return $this->glue('_', $extras, true);
    }

    public function getNamespace($extras = [])
    {
        return $this->glue('\\', $extras, true);
    }

    public function getPath($extras = [])
    {
        $app = $this->directoryList->getPath('app');
        $pieces = [$app, 'code', $this->glue(DIRECTORY_SEPARATOR, $extras, false)];
        return implode(DIRECTORY_SEPARATOR, $pieces);
    }

    public function getSlug($extras = [])
    {
        return strtolower($this->getName($extras));
    }

    protected function glue($separator, $extras, $ucExtras)
    {
        if ($ucExtras) {
            $extras = array_map( function($extra) { return ucfirst($extra); }, $extras);
        }
        return implode($separator, array_merge([$this->vendor, $this->component], $extras));
    }
}