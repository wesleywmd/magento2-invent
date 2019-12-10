<?php
namespace Wesleywmd\Invent\Api;

interface PhpRendererInterface
{
    public function getContents(DataInterface $data);
}