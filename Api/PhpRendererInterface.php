<?php
namespace Wesleywmd\Invent\Api;

interface PhpRendererInterface
{
    public function getPath(DataInterface $data);
    
    public function getContents(DataInterface $data);
}