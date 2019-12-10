<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\Cron\PhpRenderer;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Controller implements ComponentInterface
{
    private $phpRenderer;

    private $fileHelper;

    private $pathHelper;

    private $domFactory;

    private $location;

    public function __construct(
        Controller\PhpRenderer $phpRenderer,
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

    public function createPhpFile(Controller\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), $data->getPathPieces());
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);
    }

    public function createXmlFile(Controller\Data $data)
    {
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_ROUTE, Location::AREA_FRONTEND);
        $contents = $this->domFactory->create($location, Location::TYPE_ROUTE)
            ->updateElement('router', 'id', $data->getRouter())
            ->updateElement('route', 'id', $data->getFrontName(), null, ['router[@id="'.$data->getRouter().'"]'])
            ->updateElement('module', 'name', $data->getModuleName()->getName(), null, ['router[@id="'.$data->getRouter().'"]', 'route[@id="'.$data->getFrontName().'"]'])
            ->print();
        $this->fileHelper->saveFile($location, $contents, true);
    }
}