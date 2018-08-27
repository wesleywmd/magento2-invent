<?php
namespace Wesleywmd\Invent\Service\Php;

class ControllerClassRenderer
{
    private $moduleService;

    public function __construct(
        \Wesleywmd\Invent\Service\ModuleService $moduleService
    ) {
        $this->moduleService = $moduleService;
    }

    public function render($moduleName, $directories, $className)
    {
        $namespace = $this->moduleService->getNamespace($moduleName) . "\\" . implode("\\", $directories);
        return "<?php
namespace {$namespace};
 
class {$className} extends \Magento\Framework\App\Action\Action
{
    protected \$resultPageFactory;
 
    public function __construct(
        \Magento\Framework\App\Action\ContextContext \$context, 
        \Magento\Framework\View\Result\PageFactory \$resultPageFactory)
    {
        \$this->resultPageFactory = \$resultPageFactory;
        parent::__construct(\$context);
    }
 
    public function execute()
    {
        \$resultPage = \$this->resultPageFactory->create();
        return \$resultPage;
    }
}";
    }
}