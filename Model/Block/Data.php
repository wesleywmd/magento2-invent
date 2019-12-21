<?php
namespace Wesleywmd\Invent\Model\Block;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $blockName;

    public function __construct(ModuleName $moduleName, $blockName)
    {
        $directories = explode('/', $blockName);
        $directories = array_map( function($dir) { return ucfirst($dir); }, $directories);
        $className = array_pop($directories);
        $directories = array_merge(['Block'], $directories);
        parent::__construct($moduleName, $className, $directories);
        $this->blockName = $blockName;
    }

    public function getBlockName()
    {
        return $this->blockName;
    }
}