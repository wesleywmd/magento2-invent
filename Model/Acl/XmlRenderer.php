<?php
namespace Wesleywmd\Invent\Model\Acl;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Model\Component\AbstractXmlRenderer;
use Wesleywmd\Invent\Model\XmlParser\Dom;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class XmlRenderer extends AbstractXmlRenderer implements RendererInterface
{
    private $aclHelper;

    public function __construct(DomFactory $domFactory, Location $location, AclHelper $aclHelper)
    {
        parent::__construct($domFactory, $location);
        $this->aclHelper = $aclHelper;
    }

    protected function getType()
    {
        return Location::TYPE_ACL;
    }

    protected function updateDom(Dom &$dom, DataInterface $data)
    {
        /** @var Data $data */
        $xpath = $this->addResourcesNode($dom, $data);
        $parents = $this->aclHelper->getParents($data->getParentAcl());
        $prev = null;
        foreach ($parents as $parent) {
            $xpath = $this->addResourceNode($dom, $prev, $parent, $xpath);
            $prev = $parent;
        }
        $this->addResourceNode($dom, $prev, $data->getResource(), $xpath, [
            'title' => $data->getTitle(),
            'translate' => 'title',
            'sortOrder' => $data->getSortOrder()
        ]);
    }

    private function addResourcesNode(Dom &$dom, DataInterface $data)
    {
        $dom->updateElement('acl')
            ->updateElement('resources', null, null, null, ['acl']);
        return ['acl', 'resources'];
    }
    
    private function addResourceNode(Dom &$dom, $prev, $parent, $xpath, $attributes = [])
    {
        if (!is_null($prev)) {
            $xpath = array_merge($xpath, ['resource[@id="'.$prev.'"]']);
        }
        $dom->updateElement('resource', 'id', $parent, null, $xpath);
        if (!empty($attributes)) {
            $dom->updateAttributes($attributes, array_merge($xpath, ['resource[@id="'.$parent.'"]']));
        }
        return $xpath;
    }
}