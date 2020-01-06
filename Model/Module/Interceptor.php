<?php
namespace Wesleywmd\Invent\Model\Module;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataFactoryInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\InterceptorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Model\Block;
use Wesleywmd\Invent\Model\Command;
use Wesleywmd\Invent\Model\Component\BaseInterceptor;
use Wesleywmd\Invent\Model\Controller;
use Wesleywmd\Invent\Model\Cron;
use Wesleywmd\Invent\Model\Model;
use Wesleywmd\Invent\Model\ModuleNameFactory;

class Interceptor extends BaseInterceptor implements InterceptorInterface
{
    private $moduleNameFactory;
    
    private $blockDataFactory;
    
    private $blockComponent;
    
    private $commandDataFactory;
    
    private $commandComponent;
    
    private $controllerDataFactory;
    
    private $controllerComponent;
    
    private $cronDataFactory;
    
    private $cronComponent;
    
    private $modelDataFactory;
    
    private $modelComponent;

    public function __construct(
        ModuleNameFactory $moduleNameFactory,
        Block\DataFactory $blockDataFactory,
        ComponentInterface $blockComponent,
        Command\DataFactory $commandDataFactory,
        ComponentInterface $commandComponent,
        Controller\DataFactory $controllerDataFactory,
        ComponentInterface $controllerComponent,
        Cron\DataFactory $cronDataFactory,
        ComponentInterface $cronComponent,
        Model\DataFactory $modelDataFactory,
        ComponentInterface $modelComponent
    ) {
        $this->moduleNameFactory = $moduleNameFactory;
        $this->blockDataFactory = $blockDataFactory;
        $this->blockComponent = $blockComponent;
        $this->commandDataFactory = $commandDataFactory;
        $this->commandComponent = $commandComponent;
        $this->controllerDataFactory = $controllerDataFactory;
        $this->controllerComponent = $controllerComponent;
        $this->cronDataFactory = $cronDataFactory;
        $this->cronComponent = $cronComponent;
        $this->modelDataFactory = $modelDataFactory;
        $this->modelComponent = $modelComponent;
    }

    public function after(InventStyle $io, DataInterface $data)
    {
        $this->addComponents($io, 'block', 'blockName', $this->blockComponent, $this->blockDataFactory);
        $this->addComponents($io, 'command', 'commandName', $this->commandComponent, $this->commandDataFactory);
        $this->addComponents($io, 'controller', 'controllerUrl', $this->controllerComponent, $this->controllerDataFactory);
        $this->addComponents($io, 'cron', 'cronName', $this->cronComponent, $this->cronDataFactory);
        $this->addComponents($io, 'model', 'modelName', $this->modelComponent, $this->modelDataFactory);
    }

    private function addComponents(InventStyle $io, $option, $nameKey, ComponentInterface $component, DataFactoryInterface $dataFactory)
    {
        foreach( $io->getInput()->getOption($option) as $name ) {
            $data = $dataFactory->createFromArray([
                'moduleName' => $this->moduleNameFactory->create($io->getInput()->getArgument('moduleName')),
                $nameKey => $name
            ]);
            $component->addToModule($data);
            $io->success($name.' Created Successfully!');
        }
    }
}