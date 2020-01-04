<?php
namespace Wesleywmd\Invent\Model\Module;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    public function __construct(ModuleName $moduleName)
    {
        parent::__construct($moduleName, 'registration', []);
        $this->moduleName = $moduleName;
    }
}