<?php
namespace Wesleywmd\Invent\Model\Command;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Model\Component\AbstractData;
use Wesleywmd\Invent\Model\ModuleName;

class Data extends AbstractData implements DataInterface
{
    private $commandName;

    public function __construct(ModuleName $moduleName, $commandName)
    {
        $className = array_map('ucfirst', explode(':', $commandName));
        parent::__construct($moduleName, implode('', $className).'Command', ['Console', 'Command']);
        $this->commandName = $commandName;
    }

    public function getCommandName()
    {
        return $this->commandName;
    }

    public function getItemName()
    {
        return $this->moduleName->getSlug(explode(':', $this->commandName));
    }
}