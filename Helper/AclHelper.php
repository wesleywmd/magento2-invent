<?php
namespace Wesleywmd\Invent\Helper;

use Magento\Framework\Acl\AclResource\ProviderInterface;

class AclHelper
{
    private $aclProvider;

    public function __construct(ProviderInterface $aclProvider)
    {
        $this->aclProvider = $aclProvider;
    }

    public function getTree()
    {
        $tree = $this->aclProvider->getAclResources();
        return $this->getTreeChildren($tree);
    }

    private function getTreeChildren($tree)
    {
        $return = [];
        foreach ($tree as $branch) {
            $return[$branch['id']] = $this->getTreeChildren($branch['children']);
        }
        return $return;
    }

    public function findInTree($needle, $haystack = null)
    {
        if (is_null($haystack)) {
            $haystack = $this->getTree();
        }
        foreach ($haystack as $resource=>$children) {
            if ($resource === $needle) {
                return true;
            }
            if ($this->findInTree($needle, $children)) {
                return true;
            }
        }
        return false;
    }

    public function getParentTree($needle)
    {
        return $this->getParentTreeChildren($needle, $this->getTree(), []);
    }

    private function getParentTreeChildren($needle, $haystack, $parent)
    {
        foreach ($haystack as $resource=>$children) {
            if ($resource !== $needle && !$this->findInTree($needle, $children)) {
                continue;
            }
            if ($resource === $needle) {
                $parent[$resource] = [];
            }
            if ($this->findInTree($needle, $children)) {
                $parent[$resource] = $this->getParentTreeChildren($needle, $children, $parent);
            }
            return $parent;
        }
        return [];
    }

    public function getParents($needle)
    {
        if (is_null($needle)) {
            return [];
        }
        $parentTree = $this->getParentTree($needle);
        return $this->flattenParentTree($parentTree);
    }

    public function flattenParentTree($tree)
    {
        return $this->flattenParentTreeChildren($tree, []);
    }

    private function flattenParentTreeChildren($tree, $result)
    {
        foreach ($tree as $resource=>$children) {
            $result[] = $resource;
            if (!empty($children)) {
                $result = $this->flattenParentTreeChildren($children, $result);
            }
        }
        return $result;
    }

    public function getParentOptions($parent = null)
    {
        $result = [];
        $tree = $this->getTree();
        if (!is_null($parent)) {
            $tree = $this->getChildTree($tree, $parent);
        }
        foreach ($tree as $resource=>$children) {
            $result[] = $resource;
        }
        return $result;
    }

    private function getChildTree($tree, $parent)
    {
        foreach ($tree as $resource=>$children) {
            if ($resource === $parent) {
                return $children;
            }
            if ($this->findInTree($parent, $children)) {
                return $this->getChildTree($children, $parent);
            }
        }
        return [];
    }
}