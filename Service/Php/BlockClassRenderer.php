<?php
namespace Wesleywmd\Invent\Service\Php;

class BlockClassRenderer
{
    private $moduleService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService
    ) {
        $this->moduleService = $moduleService;
    }

    public function render($moduleName, $directories, $className)
    {
        $namespace = $this->moduleService->getNamespace($moduleName);
        if( !empty($directories) ) {
            $namespace .= "\\" . implode("\\", $directories);
        }
        return "<?php
namespace {$namespace};
 
class {$className} extends \Magento\Framework\View\Element\Template
{
    // @TODO implement $namespace\\$className Block
}";
    }
}