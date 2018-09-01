<?php
namespace Wesleywmd\Invent\Service\Php;

class CronClassRenderer
{
    private $moduleService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService
    ) {
        $this->moduleService = $moduleService;
    }

    public function render($moduleName, $directories, $className, $method)
    {
        return "<?php
namespace {$this->moduleService->getNamespace($moduleName, $directories)};
class {$className} {
 
    protected \$logger;
 
    public function __construct(\Psr\Log\LoggerInterface \$logger) {
        \$this->logger = \$logger;
    }
 
    public function execute() {
        \$this->logger->info(__METHOD__);
        // @TODO implement {$method} method for {$this->moduleService->getNamespace($moduleName, $directories, $className)}
        return \$this;
    }
}";
    }
}