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

    public function relativePath(ModuleName $moduleName, $directories = [])
    {
        return implode(DIRECTORY_SEPARATOR, array_merge([$moduleName->getLocation()], $directories));
    }

    public function fullPath(ModuleName $moduleName, $directories = [])
    {
        $pieces = [$this->directoryList->getPath('app'), 'code', $this->relativePath($moduleName, $directories)];
        return implode(DIRECTORY_SEPARATOR, $pieces);
    }

    public function fullPathExists(ModuleName $moduleName, $directories = [])
    {
        return $this->pathExists($this->fullPath($moduleName, $directories));
    }

    public function relativePathExists(ModuleName $moduleName, $directories = [])
    {
        return $this->pathExists($this->relativePath($moduleName, $directories));
    }

    private function pathExists($path)
    {
        return (bool) is_dir($path);
    }
}