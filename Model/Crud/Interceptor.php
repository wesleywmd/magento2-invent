<?php
namespace Wesleywmd\Invent\Model\Crud;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\InterceptorInterface;
use Wesleywmd\Invent\Console\InventStyle;
use Wesleywmd\Invent\Model\Menu;
use Wesleywmd\Invent\Model\Model;
use Wesleywmd\Invent\Model\Component\BaseInterceptor;

class Interceptor extends BaseInterceptor implements InterceptorInterface
{
    private $modelDataFactory;

    private $modelComponent;

    private $modelInterceptor;

    private $menuDataFactory;

    private $menuComponent;

    private $menuInterceptor;

    public function __construct(
        Model\DataFactory $modelDataFactory,
        ComponentInterface $modelComponent,
        Model\Interceptor $modelInterceptor,
        Menu\DataFactory $menuDataFactory,
        ComponentInterface $menuComponent,
        Menu\Interceptor $menuInterceptor
    ) {
        $this->modelDataFactory = $modelDataFactory;
        $this->modelComponent = $modelComponent;
        $this->modelInterceptor = $modelInterceptor;
        $this->menuDataFactory = $menuDataFactory;
        $this->menuComponent = $menuComponent;
        $this->menuInterceptor = $menuInterceptor;
    }

    public function before(InventStyle $io, DataInterface $data)
    {
        /** @var Data $data */
        /** @var Model\Data $modelData */
        $modelData = $this->createModelData($io, $data);
        $data->setModel($modelData);
        if (!$io->getInput()->getOption('no-model')) {
            $this->modelComponent->addToModule($modelData);
        }
        /** @var Menu\Data $menuData */
        $menuData = $this->createMenuData($io, $data);
        $data->setMenu($menuData);
        if (!$io->getInput()->getOption('no-menu')) {
            $this->menuComponent->addToModule($menuData);
        }
        return $data;
    }

    public function after(InventStyle $io, DataInterface $data)
    {
        /** @var Data $data */
        $this->modelInterceptor->after($io, $data->getModel());
        $this->menuInterceptor->after($io, $data->getMenu());
    }

    private function createModelData(InventStyle $io, DataInterface $data)
    {
        /** @var Data $data */
        return $this->modelDataFactory->createFromArray([
            'moduleName' => $data->getModuleName(),
            'modelName' => $data->getCrudName(),
            'columns' => $io->getInput()->getOption('modelColumn'),
            'tableName' => $io->getInput()->getOption('modelTableName')
        ]);
    }

    private function createMenuData(InventStyle $io, DataInterface $data)
    {
        /** @var Data $data */
        return $this->menuDataFactory->createFromArray([
            'moduleName' => $data->getModuleName(),
            'menuName' => $io->getInput()->getOption('menuName') ?: $data->getModuleName()->getSlug([$data->getCrudName(), 'menu']),
            'parentMenu' => $io->getInput()->getOption('menuParent'),
            'title' => $io->getInput()->getOption('menuTitle'),
            'sortOrder' => $io->getInput()->getOption('menuSortOrder'),
            'action' => $io->getInput()->getOption('menuAction'),
            'resource' => $io->getInput()->getOption('menuResource'),
            'no-acl' => true
        ]);
    }
}