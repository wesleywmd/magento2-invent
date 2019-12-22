<?php
namespace Wesleywmd\Invent\Api;

interface XmlRendererInterface
{
    public function getPath(DataInterface $data);

    public function getContents(DataInterface $data);
}