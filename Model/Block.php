<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;

class Block implements ComponentInterface
{
    private $phpRenderer;

    private $fileHelper;

    public function __construct(Block\PhpRenderer $phpRenderer, FileHelper $fileHelper)
    {
        $this->phpRenderer = $phpRenderer;
        $this->fileHelper = $fileHelper;
    }

    public function addToModule(DataInterface $data)
    {
        /** @var Block\Data $data */
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getPath(), $contents);
    }
}