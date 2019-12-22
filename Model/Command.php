<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\Component\AbstractComponent;

class Command extends AbstractComponent implements ComponentInterface
{
    public function __construct(
        FileHelper $fileHelper,
        Command\PhpRenderer $phpRenderer,
        Command\XmlRenderer $xmlRenderer
    ) {
        parent::__construct($fileHelper, $phpRenderer, $xmlRenderer);
    }

    public function addToModule(DataInterface $data)
    {
        $this->createPhpFile($data);
        $this->createXmlFile($data);
    }
}