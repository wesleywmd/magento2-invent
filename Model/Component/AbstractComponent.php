<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Helper\FileHelper;

abstract class AbstractComponent
{
    protected $fileHelper;

    private $phpRenderer;

    private $xmlRenderer;

    public function __construct(
        FileHelper $fileHelper,
        PhpRendererInterface $phpRenderer,
        XmlRendererInterface $xmlRenderer
    ) {
        $this->phpRenderer = $phpRenderer;
        $this->fileHelper = $fileHelper;
        $this->xmlRenderer = $xmlRenderer;
    }

    abstract public function addToModule(DataInterface $data);

    protected function createPhpFile(DataInterface $data)
    {
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getPath(), $contents);
    }

    protected function createXmlFile(DataInterface $data)
    {
        $location = $this->xmlRenderer->getPath($data);
        $contents = $this->xmlRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents, true);
    }
}