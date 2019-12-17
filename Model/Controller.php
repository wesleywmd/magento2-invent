<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\Cron\PhpRenderer;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Controller implements ComponentInterface
{
    private $phpRenderer;

    private $fileHelper;

    private $domFactory;

    private $location;

    public function __construct(
        Controller\PhpRenderer $phpRenderer,
        FileHelper $fileHelper,
        DomFactory $domFactory,
        Location $location
    ) {
        $this->phpRenderer = $phpRenderer;
        $this->fileHelper = $fileHelper;
        $this->domFactory = $domFactory;
        $this->location = $location;
    }

    public function addToModule(DataInterface $data)
    {
        $this->createPhpFile($data);
        $this->createXmlFile($data);
    }

    public function createPhpFile(Controller\Data $data)
    {
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getPath(), $contents);
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