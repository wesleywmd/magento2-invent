<?php
namespace Wesleywmd\Invent\Model\Cron;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer
{
    private $domFactory;

    private $location;

    public function __construct(DomFactory $domFactory, Location $location)
    {
        $this->domFactory = $domFactory;
        $this->location = $location;
    }

    public function getContents($location, DataInterface $data)
    {
        $dom = $this->domFactory->create($location, Location::TYPE_CRONTAB);
        $groupNodeXpath = $this->addGroupNode($dom, $data);
        $jobNodeXpath = $this->addJobNode($dom, $data, $groupNodeXpath);
        $this->addScheduleNode($dom, $data, $jobNodeXpath);
        return $dom->print();
    }

    private function addGroupNode(Dom &$dom, Data $data)
    {
        $dom->updateElement('group', 'id', $data->getGroup());
        return ['group[@id="'.$data->getGroup().'"]'];
    }

    private function addJobNode(Dom &$dom, $data, $groupNodeXpath)
    {
        $jobNodeXpath = array_merge($groupNodeXpath, ['job[@name="'.$data->getJobName().'"]']);
        $dom->updateElement('job', 'name', $data->getJobName(), null, $groupNodeXpath)
            ->updateAttributes([
                'instance' => $data->getInstance(),
                'method' => $data->getMethod()
            ], $jobNodeXpath);
        return $jobNodeXpath;
    }

    private function addScheduleNode(&$dom, $data, $jobNodeXpath)
    {
        $dom->updateElement('schedule', null, null, $data->getSchedule(), $jobNodeXpath);
        return array_merge($jobNodeXpath, ['schedule']);
    }
}