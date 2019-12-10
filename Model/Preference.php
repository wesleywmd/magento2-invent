<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Preference implements ComponentInterface
{
    private $xmlRenderer;

    private $fileHelper;

    private $pathHelper;

    private $domFactory;

    private $location;

    public function __construct(
        FileHelper $fileHelper,
        PathHelper $pathHelper,
        DomFactory $domFactory,
        Location $location
    ) {
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
        $this->createXmlFile($data);
    }

    private function createXmlFile(Preference\Data $data)
    {
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_DI, $data->getArea());
        $contents = $this->domFactory->create($location, Location::TYPE_DI)
            ->updateElement('preference', 'for', $data->getFor())
            ->updateAttribute('type', $data->getType(), ['preference[@for="'.$data->getFor().'"]'])
            ->print();
        $this->fileHelper->saveFile($location, $contents, true);
    }
}