<?php
namespace Wesleywmd\Invent\Api;

use Wesleywmd\Invent\Console\InventStyle;

interface ValidatorInterface
{
    public function validate(InventStyle $io);
}