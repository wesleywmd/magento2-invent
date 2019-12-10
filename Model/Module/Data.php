<?php
namespace Wesleywmd\Invent\Model\Module;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\ModuleName;

class Data implements DataInterface
{
    private $moduleName;
    
    public function __construct(ModuleName $moduleName)
    {
        $this->moduleName = $moduleName;
    }
    
    public function getModuleName()
    {
        return $this->moduleName;
    }
}