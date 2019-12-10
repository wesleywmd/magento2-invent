<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Command implements ComponentInterface
{
    private $phpRenderer;

    private $fileHelper;

    private $pathHelper;

    private $domFactory;

    private $location;

    public function __construct(
        Command\PhpRenderer $phpRenderer,
        FileHelper $fileHelper,
        PathHelper $pathHelper,
        DomFactory $domFactory,
        Location $location
    ) {
        $this->phpRenderer = $phpRenderer;
        $this->fileHelper = $fileHelper;
        $this->pathHelper = $pathHelper;
        $this->domFactory = $domFactory;
        $this->location = $location;
    }

    public function addToModule(DataInterface $data)
    {
        if (!$this->pathHelper->fullPathExists($data->getModuleName())) {
            throw new \Exception('Module does not exist');
        }
        $this->createPhpFile($data);
        $this->createXmlFile($data);
    }

    private function createPhpFile(Command\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), $data->getPathPieces());
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);
    }

    private function createXmlFile(Command\Data $data)
    {
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_DI, Location::AREA_GLOBAL);
        $contents = $this->domFactory->create($location, Location::TYPE_DI)
            ->updateElement('type', 'name', 'Magento\\Framework\\Console\\CommandList')
            ->updateElement('arguments', null, null, null, ['type[@name="Magento\\Framework\\Console\\CommandList"]'])
            ->updateElement('argument', 'name', 'commands', null, ['type[@name="Magento\\Framework\\Console\\CommandList"]', 'arguments'])
            ->updateElement('item', 'name', $data->getItemName(), $data->getInstance(), ['type[@name="Magento\\Framework\\Console\\CommandList"]', 'arguments', 'argument[@name="commands"]'])
            ->updateAttribute('xsi:type', 'object', ['type[@name="Magento\\Framework\\Console\\CommandList"]', 'arguments', 'argument[@name="commands"]', 'item[@name="'.$data->getItemName().'"]'])
            ->print();
        $this->fileHelper->saveFile($location, $contents, true);
    }
}