<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;

class Block implements ComponentInterface
{
    private $phpRenderer;

    private $fileHelper;

    private $pathHelper;

    public function __construct(
        Block\PhpRenderer $phpRenderer,
        FileHelper $fileHelper,
        PathHelper $pathHelper
    ) {
        $this->phpRenderer = $phpRenderer;
        $this->fileHelper = $fileHelper;
        $this->pathHelper = $pathHelper;
    }

    public function addToModule(DataInterface $data)
    {
        if (!$this->pathHelper->fullPathExists($data->getModuleName())) {
            throw new \Exception('Module does not exist');
        }
        $this->createPhpFile($data);
    }
    
    private function createPhpFile(Block\Data $data)
    {
        $contents = $this->phpRenderer->getContents($data);
        $location = $this->pathHelper->fullPath($data->getModuleName(), $data->getPathPieces());
        $this->fileHelper->saveFile($location, $contents);
    }
}