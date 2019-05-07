<?php
namespace Wesleywmd\Invent\Model\ModuleForge\XmlFile;

use Wesleywmd\Invent\Api\Data\DomInterface;
use Wesleywmd\Invent\Api\Data\XmlFileInterface;

class Dom implements DomInterface
{
    private $moduleHelper;

    private $xmlFile;

    private $dom;

    public function __construct(
        \Wesleywmd\Invent\Helper\ModuleHelper $moduleHelper,
        \Wesleywmd\Invent\Api\Data\XmlFileInterface $xmlFile
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->xmlFile = $xmlFile;

        $this->init();
    }

    private function init()
    {
        $filePath = $this->moduleHelper->getFilePath(
            $this->xmlFile->getModule(),
            $this->xmlFile->getFileName(),
            $this->xmlFile->getDirectories()
        );
        if( is_file($filePath) ) {
            $this->dom = simplexml_load_file($filePath);
        } else {
            $this->dom = new \SimpleXMLElement($this->xmlFile->getDefaultDom());
        }
    }

    public function updateElement($node, $key = null, $value = null, $text = null, $xpath = [])
    {
        $xpath = $this->resolveXpath($xpath);
        $thisXpath = $xpath . "/$node" . (is_null($key)?"":"[@$key=\"$value\"]");
        if( empty($this->dom->xpath($thisXpath)) ) {
            if( !is_null($key) ) {
                $this->dom->xpath($xpath)[0]->addChild($node, $text)->addAttribute($key, $value);
            } else {
                $this->dom->xpath($xpath)[0]->addChild($node, $text);
            }
        }
        return $this;
    }

    public function updateAttribute($attribute, $value, $xpath = [])
    {
        $xpath = $this->resolveXpath($xpath);
        if( ! empty( $this->dom->xpath($xpath) ) ) {
            $this->dom->xpath($xpath)[0]->addAttribute("xmlns:".$attribute, $value);
        }
        return $this;
    }

    public function toString()
    {
        $xml = new \DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        $xml->loadXML($this->dom->asXML());
        return str_replace("  ", "    ", $xml->saveXml());
    }

    private function resolveXpath($xpath)
    {
        if( empty($xpath) ) {
            return XmlFileInterface::DEFAULT_XPATH;
        }
        if( is_array($xpath) ) {
            return XmlFileInterface::DEFAULT_XPATH . "/" . implode("/", $xpath);
        }
        if( is_string($xpath) ) {
            return XmlFileInterface::DEFAULT_XPATH . "/" . $xpath;
        }
    }
}