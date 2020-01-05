<?php
namespace Wesleywmd\Invent\Api;

use Symfony\Component\Console\Input\InputInterface;

interface DataFactoryInterface
{
    public function create(InputInterface $input);
}