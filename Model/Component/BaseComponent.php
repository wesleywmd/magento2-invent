<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\PhpRendererInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Helper\FileHelper;

class BaseComponent implements ComponentInterface
{
    protected $fileHelper;

    private $phpRenderers;

    private $xmlRenderers;

    public function __construct(
        FileHelper $fileHelper,
        array $phpRenderers = [],
        array $xmlRenderers = []
    ) {
        $this->fileHelper = $fileHelper;
        $this->phpRenderers = $phpRenderers;
        $this->xmlRenderers = $xmlRenderers;
    }

    public function addToModule(DataInterface $data)
    {
        foreach ($this->phpRenderers as $phpRenderer) {
            $this->createPhpFile($phpRenderer, $data);
        }
        foreach ($this->xmlRenderers as $xmlRenderer) {
            $this->createXmlFile($xmlRenderer, $data);
        }
    }

    protected function createPhpFile(PhpRendererInterface $phpRenderer, DataInterface $data)
    {
        $contents = $phpRenderer->getContents($data);
        $this->fileHelper->saveFile($phpRenderer->getPath($data), $contents);
    }

    protected function createXmlFile(XmlRendererInterface $xmlRenderer, DataInterface $data)
    {
        $contents = $xmlRenderer->getContents($data);
        $this->fileHelper->saveFile($xmlRenderer->getPath($data), $contents, true);
    }
}