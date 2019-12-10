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

    private $pathHelper;

    private $domFactory;

    private $location;

    public function __construct(
        Cron\PhpRenderer $phpRenderer,
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

    private function createPhpFile(Cron\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), $data->getPathPieces());
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);
    }

    private function createXmlFile(Cron\Data $data)
    {
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_CRONTAB, Location::AREA_GLOBAL);
        $contents = $this->domFactory->create($location, Location::TYPE_CRONTAB)
            ->updateElement('group', 'id', $data->getGroup())
            ->updateElement('job', 'name', $data->getJobName(), null, ['group[@id="'.$data->getGroup().'"]'])
            ->updateAttribute('instance', $data->getInstance(), ['group[@id="'.$data->getGroup().'"]', 'job[@name="'.$data->getJobName().'"]'])
            ->updateAttribute('method', $data->getMethod(), ['group[@id="'.$data->getGroup().'"]', 'job[@name="'.$data->getJobName().'"]'])
            ->updateElement('schedule', null, null, $data->getSchedule(), ['group[@id="'.$data->getGroup().'"]', 'job[@name="'.$data->getJobName().'"]'])
            ->print();
        $this->fileHelper->saveFile($location, $contents, true);
    }
}