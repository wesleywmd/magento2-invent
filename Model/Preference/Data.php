<?php
namespace Wesleywmd\Invent\Model\Preference;


use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $area;

    private $for;

    private $type;

    public function __construct(ModuleName $moduleName, $for, $type, $area)
    {
        $this->moduleName = $moduleName;
        $this->for = $for;
        $this->type = $type;
        $this->area = $area;
    }

    public function getFor()
    {
        return $this->for;
    }

    public function getType()
    {
        return $this->type;
    }

    public function getArea()
    {
        return $this->area;
    }
}