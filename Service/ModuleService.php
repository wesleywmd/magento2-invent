<?php
namespace Wesleywmd\Invent\Service;

use Wesleywmd\Invent\Exception\ModuleServiceException;

class ModuleService
{
    private $directoryList;
    private $fullModuleList;
    private $manager;
    private $status;

    public function __construct(
        \Magento\Framework\Module\FullModuleList $fullModuleList,
        \Magento\Framework\Module\Status $status,
        \Magento\Framework\Module\Manager $manager,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList
    ) {
        $this->directoryList = $directoryList;
        $this->fullModuleList = $fullModuleList;
        $this->manager = $manager;
        $this->status = $status;
    }

    public function getDirectory($moduleName, $extras = [])
    {
        $directory = $this->directoryList->getPath('app');
        $directory .= DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR;
        $directory .= str_replace("_",DIRECTORY_SEPARATOR, $moduleName);
        if( !empty($extras) ) {
            $directory .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $extras);
        }
        return $directory;
    }

    public function isDirectory($moduleName)
    {
        return is_dir($this->getDirectory($moduleName));
    }

    public function isFile($moduleName, $fileName, $extras = [])
    {
        $filePath = $this->getDirectory($moduleName, $extras) . DIRECTORY_SEPARATOR . $fileName;
        return is_file($filePath);
    }

    public function isEnabled($moduleName)
    {
        return $this->manager->isEnabled($moduleName);
    }

    public function isRegistered($moduleName)
    {
        return $this->fullModuleList->has($moduleName);
    }

    public function isComposer($moduleName)
    {
        return $this->isRegistered($moduleName) && !$this->isDirectory($moduleName);
    }

    public function isCustom($moduleName)
    {
        return $this->isRegistered($moduleName) && $this->isDirectory($moduleName);
    }

    public function isOutputEnabled($moduleName)
    {
        return $this->manager->isOutputEnabled($moduleName);
    }

    public function get($moduleName)
    {
        return $this->fullModuleList->getOne($moduleName);
    }

    public function makeDirectory($moduleName, $extras = [])
    {
        if( $this->isDirectory($moduleName) ) {
            throw new ModuleServiceException("Cannot Create Module. Directory already exists.");
        }
        if( $this->isRegistered($moduleName) ) {
            throw new ModuleServiceException("Cannot Create Module. Module already registered.");
        }
        $directory = $this->getDirectory($moduleName, $extras);
        mkdir($directory, 0777, true);
    }

    public function makeFile($fileName, $contents, $moduleName, $extras = [])
    {
        $filePath = $this->getDirectory($moduleName, $extras);
        if( !is_dir($filePath) ) {
            mkdir($filePath, 0777, true);
        }
        $filePath .= DIRECTORY_SEPARATOR . $fileName;
        $registrationHandle = fopen($filePath, 'w') or die('Cannot open file:  '.$filePath);
        fwrite($registrationHandle, $contents);
    }

    public function convertToXml($array, $root = "config")
    {
        $xml = new \SimpleXMLElement($root ? '<' . $root . '/>' : '<root/>');
        array_walk_recursive($array, function($value, $key)use($xml){
            $xml->addChild($key, $value);
        });
        return $xml->asXML();
    }

    public function getNamespace($moduleName)
    {
        return str_replace("_", "\\", $moduleName);
    }

}