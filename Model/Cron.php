<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Cron implements ComponentInterface
{
    private $phpRenderer;

    private $fileHelper;

    private $location;

    private $xmlRenderer;

    public function __construct(
        Cron\PhpRenderer $phpRenderer,
        FileHelper $fileHelper,
        Location $location,
        Cron\XmlRenderer $xmlRenderer
    ) {
        $this->phpRenderer = $phpRenderer;
        $this->fileHelper = $fileHelper;
        $this->location = $location;
        $this->xmlRenderer = $xmlRenderer;
    }

    public function addToModule(DataInterface $data)
    {
        if (!$this->pathHelper->fullPathExists($data->getModuleName())) {
            throw new \Exception('Module does not exist');
        }

        $this->createPhpFile($data);
        $this->createXmlFile($data);
    }

    private function createPhpFile(Cron\Data $data)
    {
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getPath(), $contents);
    }

    private function createXmlFile(Cron\Data $data)
    {
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_CRONTAB, Location::AREA_GLOBAL);
        $contents = $this->xmlRenderer->getContents($location, $data);
        $this->fileHelper->saveFile($location, $contents, true);
    }
}