<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\InterceptorInterface;
use Wesleywmd\Invent\Console\InventStyle;

class BaseInterceptor implements InterceptorInterface
{
    public function before(InventStyle $io, DataInterface $data)
    {
        return;
    }
    
    public function after(InventStyle $io, DataInterface $data)
    {
        return;
    }
}