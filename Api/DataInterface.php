<?php
namespace Wesleywmd\Invent\Api;

use Wesleywmd\Invent\Model\ModuleName;

interface DataInterface
{
    public function getClassName();

    public function getDirectories();

    public function getInstance();

    /**
     * @return ModuleName
     */
    public function getModuleName();

    public function getNamespace();

    public function getPath();
}