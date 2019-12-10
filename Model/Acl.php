<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\AclHelper;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;
use Zend\Validator\Sitemap\Loc;

class Acl implements ComponentInterface
{
    private $fileHelper;

    private $pathHelper;

    private $domFactory;

    private $location;

    private $aclHelper;

    public function __construct(
        FileHelper $fileHelper,
        PathHelper $pathHelper,
        DomFactory $domFactory,
        Location $location,
        AclHelper $aclHelper
    ) {
        $this->fileHelper = $fileHelper;
        $this->pathHelper = $pathHelper;
        $this->domFactory = $domFactory;
        $this->location = $location;
        $this->aclHelper = $aclHelper;
    }

    public function addToModule(DataInterface $data)
    {
        /** @var Acl\Data $data */
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_ACL, Location::AREA_GLOBAL);
        $dom = $this->domFactory->create($location, Location::TYPE_ACL)
            ->updateElement('acl')
            ->updateElement('resources', null, null, null, ['acl']);
        $parents = $this->aclHelper->getParents($data->getParentAcl());
        $prev = null;
        $xpath = ['acl', 'resources'];
        foreach ($parents as $parent) {
            if (!is_null($prev)) {
                $xpath = array_merge($xpath, ['resource[@id="'.$prev.'"]']);
            }
            $dom->updateElement('resource', 'id', $parent, null, $xpath);
            $prev = $parent;
        }
        if (!is_null($prev)) {
            $xpath = array_merge($xpath, ['resource[@id="'.$prev.'"]']);
        }
        $dom->updateElement('resource', 'id', $data->getResource(), null, $xpath)
            ->updateAttributes([
                'title' => $data->getTitle(),
                'translate' => 'title',
                'sortOrder' => $data->getSortOrder()
            ], array_merge($xpath, ['resource[@id="'.$data->getResource().'"]']));
        $contents = $dom->print();
        $this->fileHelper->saveFile($location, $contents, true);
    }
}