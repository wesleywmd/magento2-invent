<?php
namespace Wesleywmd\Invent\Model\Cron;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\XmlRendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements XmlRendererInterface
{
    protected function getType()
    {
        return Location::TYPE_CRONTAB;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $groupNodeXpath = $this->addKeyedNode($dom, 'group', 'id', $data->getGroup());
        $jobNodeXpath = $this->addJobNode($dom, $data, $groupNodeXpath);
        $this->addScheduleNode($dom, $data, $jobNodeXpath);
    }

    private function addJobNode(Dom &$dom, DataInterface $data, $groupNodeXpath)
    {
        $jobNodeXpath = array_merge($groupNodeXpath, ['job[@name="'.$data->getJobName().'"]']);
        $dom->updateElement('job', 'name', $data->getJobName(), null, $groupNodeXpath)
            ->updateAttributes([
                'instance' => $data->getInstance(),
                'method' => $data->getMethod()
            ], $jobNodeXpath);
        return $jobNodeXpath;
    }

    private function addScheduleNode(Dom &$dom, DataInterface $data, $jobNodeXpath)
    {
        $dom->updateElement('schedule', null, null, $data->getSchedule(), $jobNodeXpath);
        return array_merge($jobNodeXpath, ['schedule']);
    }
}