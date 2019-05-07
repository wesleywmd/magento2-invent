<?php
namespace Wesleywmd\Invent\Helper;

use Wesleywmd\Invent\Api\Data\DomInterface;

class ModuleHelper
{
    private $directoryList;
    private $fullModuleList;
    private $moduleManager;
    private $phpClassRenderer;

    public function __construct(
        \Magento\Framework\Module\FullModuleList $fullModuleList,
        \Magento\Framework\Module\Manager $moduleManager,
        \Magento\Framework\App\Filesystem\DirectoryList $directoryList,
        \Wesleywmd\Invent\Model\ModuleForge\PhpClass\Renderer $phpClassRenderer
    ) {
        $this->directoryList = $directoryList;
        $this->fullModuleList = $fullModuleList;
        $this->moduleManager = $moduleManager;
        $this->phpClassRenderer = $phpClassRenderer;
    }

    public function isFile($moduleName, $fileName, $directories = [])
    {
        return is_file($this->getFilePath($moduleName, $fileName, $directories));
    }

    public function isEnabled($moduleName)
    {
        return $this->moduleManager->isEnabled($moduleName);
    }

    public function isRegistered($moduleName)
    {
        return $this->fullModuleList->has($moduleName);
    }

    public function isComposer($moduleName)
    {
        return $this->isRegistered($moduleName) && !is_dir($this->getDirectoryPath($moduleName));
    }

    public function isCustom($moduleName)
    {
        return $this->isRegistered($moduleName) && is_dir($this->getDirectoryPath($moduleName));
    }

    public function isOutputEnabled($moduleName)
    {
        return $this->moduleManager->isOutputEnabled($moduleName);
    }

    public function getModule($moduleName)
    {
        return $this->fullModuleList->getOne($moduleName);
    }

    public function getDirectoryPath($moduleName, $directories = [])
    {
        $directory = $this->directoryList->getPath("app") .
            DIRECTORY_SEPARATOR . "code" . DIRECTORY_SEPARATOR .
            str_replace("_", DIRECTORY_SEPARATOR, $moduleName);

        if( !empty($directories) ) {
            $directory .= DIRECTORY_SEPARATOR . implode(DIRECTORY_SEPARATOR, $directories);
        }

        return $directory;
    }

    public function getFilePath($moduleName, $fileName, $directories = [])
    {
        return $this->getDirectoryPath($moduleName, $directories) . DIRECTORY_SEPARATOR . $fileName;
    }

    public function readXmlFile($moduleName, $fileName, $directories = [])
    {
        $filePath = $this->getFilePath($moduleName, $fileName, $directories);
        if( is_file($filePath) ) {
            return simplexml_load_file($filePath);
        } else {
            return new \SimpleXMLElement(\Wesleywmd\Invent\Api\Data\XmlFileInterface::DEFAULT_XML_MODULE);
        }
    }

    public function makePhpFile($moduleName, $contents, $fileName, $directories = [])
    {
        $this->makeFile($moduleName, $contents, $fileName, $directories);
    }

    public function makePhpClass(\Wesleywmd\Invent\Api\Data\PhpClassInterface $phpClass)
    {
        $contents = $this->phpClassRenderer->phpClassToString($phpClass);
        $this->makeFile($phpClass->getModule(), $contents, $phpClass->getFileName(), $phpClass->getDirectories());
    }

    public function makePhpInterface(\Wesleywmd\Invent\Api\Data\PhpClassInterface $phpClass)
    {
        $contents = $this->phpClassRenderer->interfaceToString($phpClass);
        $this->makeFile($phpClass->getModule(), $contents, $phpClass->getFileName(), $phpClass->getDirectories());
    }

    public function makeXmlFile(\Wesleywmd\Invent\Api\Data\XmlFileInterface $xmlFile, \Wesleywmd\Invent\Api\Data\DomInterface $dom)
    {
        $this->makeFile($xmlFile->getModule(), $dom->toString(), $xmlFile->getFileName(), $xmlFile->getDirectories());
    }

    private function makeFile($moduleName, $contents, $fileName, $directories = [])
    {
        $directoryPath = $this->getDirectoryPath($moduleName, $directories);
        if( !is_dir( $directoryPath) ) {
            mkdir($directoryPath, 0777, true);
        }

        $filePath = $this->getFilePath($moduleName, $fileName, $directories);
        $handle = fopen($filePath, "w");
        fwrite($handle, $contents);
        fclose($handle);
    }

}