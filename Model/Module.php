<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Module implements ComponentInterface
{
    private $phpRenderer;

    private $fileHelper;

    private $pathHelper;

    private $domFactory;

    private $location;

    public function __construct(
        Module\PhpRenderer $phpRenderer,
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
        if( is_dir($this->pathHelper->fullPath($data->getModuleName())) ) {
            throw new \Exception("Cannot Create Module, directory already exists.");
        }

        $location = $this->pathHelper->fullPath($data->getModuleName(), ['registration.php']);
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);

        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_MODULE, Location::AREA_GLOBAL);
        $contents = $this->domFactory->create($location, Location::TYPE_MODULE)
            ->updateElement('module', 'name', $data->getModuleName()->getName())
            ->updateAttribute('setup_version', '0.0.1', ['module[@name="'.$data->getModuleName()->getName().'"]']);
        $this->fileHelper->saveFile($location, $contents, true);
    }
}