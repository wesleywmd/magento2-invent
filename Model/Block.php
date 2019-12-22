<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\Component\AbstractComponent;

class Block extends AbstractComponent implements ComponentInterface
{
    public function __construct(
        FileHelper $fileHelper,
        Block\PhpRenderer $phpRenderer
    ) {
        parent::__construct($fileHelper, $phpRenderer);
    }

    public function addToModule(DataInterface $data)
    {
        $this->createPhpFile($data);
    }
}