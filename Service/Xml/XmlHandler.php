<?php
namespace Wesleywmd\Invent\Service\Xml;

class XmlHandler
{
    const TYPE_DI = "di.xml";
    const TYPE_ROUTE = "route.xml";
    const TYPE_MODULE = "module.xml";

    private $moduleService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService
    ) {
        $this->moduleService = $moduleService;
    }

    public function reformatXmlString($xmlString)
    {
        $xml = new \DOMDocument();
        $xml->preserveWhiteSpace = false;
        $xml->formatOutput = true;
        $xml->loadXML($xmlString);
        return str_replace("  ", "    ", $xml->saveXml());
    }

    public function loadFileContents($type, $moduleName, $extras = [])
    {
        $fileName = $this->moduleService->getDirectory($moduleName, $extras) . DIRECTORY_SEPARATOR . $type;
        return simplexml_load_file($fileName);
    }

    /**
     * @param \SimpleXMLElement  $xml
     * @param                    $xpath
     * @param                    $node
     * @param string|null        $type
     * @param string|null        $value
     * @param string|null        $text
     *
     * @return string
     */
    public function loadElementIfNotExists(&$xml, $xpath, $node, $type = null, $value = null, $text = null)
    {
        $newXpath = "{$xpath}/{$node}";
        if( !is_null($type) ) {
            $newXpath .= "[@{$type}=\"{$value}\"]";
        }

        if( empty($xml->xpath($newXpath)) ) {
            if( !is_null($type) ) {
                $xml->xpath($xpath)[0]->addChild($node, $text)->addAttribute($type, $value);
            } else {
                $xml->xpath($xpath)[0]->addChild($node, $text);
            }
        }

        return $newXpath;
    }

    public function updateAttribute(&$xml, $xpath, $attribute, $value)
    {
        if( empty( $xml->xpath("{$xpath}/@{$attribute}") ) ) {
            $xml->xpath($xpath)[0]->addAttribute("xmlns:".$attribute, $value);
        } else {
            $xml->xpath($xpath)[0][$attribute] = $value;
        }
    }

}