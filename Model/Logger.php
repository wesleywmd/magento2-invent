<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Logger implements ComponentInterface
{
    private $phpRenderer;

    private $handlerPhpRenderer;

    private $fileHelper;

    private $domFactory;

    private $location;

    public function __construct(
        Logger\PhpRenderer $phpRenderer,
        Logger\HandlerPhpRenderer $handlerPhpRenderer,
        FileHelper $fileHelper,
        DomFactory $domFactory,
        Location $location
    ) {
        $this->phpRenderer = $phpRenderer;
        $this->handlerPhpRenderer = $handlerPhpRenderer;
        $this->fileHelper = $fileHelper;
        $this->domFactory = $domFactory;
        $this->location = $location;
    }

    public function addToModule(DataInterface $data)
    {
        $this->createLoggerPhpFile($data);
        $this->createHandlerPhpFile($data);
        $this->createXmlFile($data);
    }

    private function createLoggerPhpFile(Logger\Data $data)
    {
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getPath('Logger.php'), $contents);
    }

    private function createHandlerPhpFile(Logger\Data $data)
    {
        $contents = $this->handlerPhpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getPath('Handler.php'), $contents);
    }

    private function createXmlFile(Logger\Data $data)
    {
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_DI, Location::AREA_GLOBAL);
        $handlerInstance = $data->getModuleName()->getNamespace(['Logger', 'Handler']);
        $loggerInstance = $data->getModuleName()->getNamespace(['Logger', 'Logger']);
        $contents = $this->domFactory->create($location, Location::TYPE_DI)
            ->updateElement('type', 'name', $handlerInstance)
            ->updateElement('arguments', null, null, null, ['type[@name="'.$handlerInstance.'"]'])
            ->updateElement('argument', 'name', 'filesystem', 'Magento\Framework\Filesystem\Driver\File', ['type[@name="'.$handlerInstance.'"]', 'arguments'])
            ->updateAttribute('xsi:type', 'object', ['type[@name="'.$handlerInstance.'"]', 'arguments', 'argument[@name="filesystem"]'])
            ->updateElement('type', 'name', $loggerInstance)
            ->updateElement('arguments', null, null, null, ['type[@name="'.$loggerInstance.'"]'])
            ->updateElement('argument', 'name', 'name', $data->getLoggerName(), ['type[@name="'.$loggerInstance.'"]', 'arguments'])
            ->updateAttribute('xsi:type', 'string', ['type[@name="'.$loggerInstance.'"]', 'arguments', 'argument[@name="name"]'])
            ->updateElement('argument', 'name', 'handlers', null, ['type[@name="'.$loggerInstance.'"]', 'arguments'])
            ->updateAttribute('xsi:type', 'array', ['type[@name="'.$loggerInstance.'"]', 'arguments', 'argument[@name="handlers"]'])
            ->updateElement('item', 'name', 'system', $handlerInstance, ['type[@name="'.$loggerInstance.'"]', 'arguments', 'argument[@name="handlers"]'])
            ->updateAttribute('xsi:type', 'object', ['type[@name="'.$loggerInstance.'"]', 'arguments', 'argument[@name="handlers"]', 'item[@name="system"]'])
            ->print();
        $this->fileHelper->saveFile($location, $contents, true);
    }
}