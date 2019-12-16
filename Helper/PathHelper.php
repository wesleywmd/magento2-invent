<?php
namespace Wesleywmd\Invent\Helper;

use Magento\Framework\App\Filesystem\DirectoryList;
use Wesleywmd\Invent\Model\ModuleName;

class PathHelper
{
    private $directoryList;

    public function __construct(DirectoryList $directoryList)
    {
        $this->directoryList = $directoryList;
    }

    public function fullPath(ModuleName $moduleName, $directories = [])
    {
        return $moduleName->getPath($directories);
    }

    public function fullPathExists(ModuleName $moduleName, $directories = [])
    {
        return (bool) is_dir($this->fullPath($moduleName, $directories));
    }
}