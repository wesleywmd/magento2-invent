<?php
namespace Wesleywmd\Invent\Model\Controller;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $controllerUrl;

    private $router;

    private $frontName;

    public function __construct(ModuleName $moduleName, $controllerUrl, $router)
    {
        $directories = array_reverse(explode('/', $controllerUrl));
        $this->frontName = array_pop($directories);
        $directories = array_reverse($directories);
        $className = ucfirst(array_pop($directories));
        $directories = array_map( function($dir) { return ucfirst($dir); }, $directories);
        $directories = array_merge(['Controller'], $directories);
        parent::__construct($moduleName, $className, $directories);
        $this->controllerUrl = $controllerUrl;
        $this->router = $router;
    }

    public function getControllerUrl()
    {
        return $this->controllerUrl;
    }

    public function getRouter()
    {
        return $this->router;
    }

    public function getFrontName()
    {
        return $this->frontName;
    }
}