<?php
namespace Wesleywmd\Invent\Api;

interface RendererInterface
{
    public function getForce();

    public function getPath(DataInterface $data);

    public function getContents(DataInterface $data);
}