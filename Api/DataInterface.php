<?php
namespace Wesleywmd\Invent\Api;

interface DataInterface
{
    public function getClassName();

    public function getDirectories();

    public function getInstance();

    public function getModuleName();

    public function getNamespace();

    public function getPath();
}