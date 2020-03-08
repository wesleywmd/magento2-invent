<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

abstract class AbstractXmlRenderer
{
    private $domFactory;

    private $location;

    public function __construct(DomFactory $domFactory, Location $location)
    {
        $this->domFactory = $domFactory;
        $this->location = $location;
    }

    abstract protected function getType();

    abstract protected function updateDom(Dom &$dom, DataInterface $data);

    public function getPath(DataInterface $data)
    {
        return $this->location->getPath($data->getModuleName(), $this->getType(), $this->getArea($data));
    }
    
    public function getForce()
    {
        return true;
    }

    public function getContents(DataInterface $data)
    {
        $dom = $this->domFactory->create($this->getPath($data), $this->getType());
        $this->updateDom($dom, $data);
        return $dom->print();
    }

    protected function getArea(DataInterface $data)
    {
        return Location::AREA_GLOBAL;
    }

    protected function spaceAttributesProperly($contents, $attributes, $padding = 14)
    {
        $spaces = str_pad("\n", $padding);
        foreach ($attributes as $attribute) {
            $contents = str_replace(
                ' '.$attribute.'=',
                $spaces.$attribute.'=',
                $contents
            );
        }
        return $contents;
    }

    protected function addKeyedNode(Dom &$dom, $node, $key, $value, $xpath = [])
    {
        $dom->updateElement($node, $key, $value, null, $xpath);
        return array_merge($xpath, [$node.'[@'.$key.'="'.$value.'"]']);
    }
}