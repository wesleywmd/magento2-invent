<?php
namespace Wesleywmd\Invent\Api;

use Wesleywmd\Invent\Console\InventStyle;

interface InterceptorInterface
{
    public function before(InventStyle $io, DataInterface $data);

    public function after(InventStyle $io, DataInterface $data);
}