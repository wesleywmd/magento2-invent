<?php
namespace Wesleywmd\Invent\Service\Php;

class CommandClassRenderer
{
    private $moduleService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService
    ) {
        $this->moduleService = $moduleService;
    }

    public function getClassName($commandName)
    {
        $className = "";
        foreach( explode(":", $commandName) as $piece) {
            $className .= ucfirst($piece);
        }
        $className .= "Command";
        return $className;
    }

    public function getNamespace($moduleName, $commandName = null)
    {
        $namespace = "{$this->moduleService->getNamespace($moduleName)}\\Console\\Command";
        if( !is_null($commandName) ) {
            $className = $this->getClassName($commandName);
            $namespace = "{$namespace}\\{$className}";
        }
        return $namespace;
    }

    public function render($moduleName, $commandName)
    {
        return "<?php
namespace {$this->getNamespace($moduleName)};

use Symfony\\Component\\Console\\Command\\Command;
use Symfony\\Component\\Console\\Input\\InputInterface;
use Symfony\\Component\\Console\\Output\\OutputInterface;

class {$this->getClassName($commandName)} extends Command
{

    protected function configure()
    {
        \$this->setName(\"{$commandName}\");
        //    ->setDescription(\"\");  @TODO add description to {$commandName}
        // @TODO implement configure method for {$commandName}
    }

    protected function execute(InputInterface \$input, OutputInterface \$output)
    {
        // @TODO implement execute method for {$commandName}
    }

}";
    }
}