<?php
namespace Wesleywmd\Invent\Model\XmlParser;

class Dom
{
    private $dom;

    private $parentNode;

    public function __construct($dom, $parentNode)
    {
        $this->dom = $dom;
        $this->parentNode = $parentNode;
    }

    public function print()
    {
        $xml = new \DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        $xml->loadXML($this->dom->asXML());
        return str_replace("  ", "    ", $xml->saveXml());
    }

    public function node($name, $attributes = [], $text = null, $xpath = [])
    {
        $key = $this->array_key_first($attributes);
        $value = array_shift($attributes);
        $this->updateElement($name, $key, $value, $text, $xpath);
        $newXpath = array_merge($xpath, [$name.(is_null($key)?'':'[@'.$key.'="'.$value.'"]')]);
        $this->updateAttributes($attributes, $newXpath);
        return $newXpath;
    }

    public function updateElement($node, $key = null, $value = null, $text = null, $xpath = [])
    {
        $xpath = $this->resolveXpath($xpath);
        $thisXpath = $xpath . '/' . $node . (is_null($key)?'':'[@'.$key.'="'.$value.'"]');
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
            $this->dom->xpath($xpath)[0]->addAttribute('xmlns:'.$attribute, $value);
        }
        return $this;
    }
    
    public function updateAttributes($attributes, $xpath = [])
    {
        foreach ($attributes as $key=>$value) {
            $this->updateAttribute($key, $value, $xpath);
        }
        return $this;
    }

    private function resolveXpath($xpath)
    {
        if( !is_array($xpath) ) {
            $xpath = [$xpath];
        }
        return '/' . implode('/', array_merge([$this->parentNode], $xpath));
    }

    private function array_key_first($array)
    {
        if (!function_exists('array_key_first')) {
            foreach($array as $key => $unused) {
                return $key;
            }
            return null;
        }
        return array_key_first($array);
    }
}