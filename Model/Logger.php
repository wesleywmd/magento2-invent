<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\Component\AbstractComponent;

class Logger extends AbstractComponent implements ComponentInterface
{
    private $handlerPhpRenderer;

    public function __construct(
        FileHelper $fileHelper,
        Logger\PhpRenderer $phpRenderer,
        Logger\XmlRenderer $xmlRenderer,
        Logger\HandlerPhpRenderer $handlerPhpRenderer
    ) {
        parent::__construct($fileHelper, $phpRenderer,$xmlRenderer);
        $this->handlerPhpRenderer = $handlerPhpRenderer;
    }

    public function addToModule(DataInterface $data)
    {
        $this->createPhpFile($data);
        $this->createHandlerPhpFile($data);
        $this->createXmlFile($data);
    }

    private function createHandlerPhpFile(Logger\Data $data)
    {
        $contents = $this->handlerPhpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getHandlerPath(), $contents);
    }
}