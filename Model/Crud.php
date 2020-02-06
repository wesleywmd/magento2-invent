<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Crud implements ComponentInterface
{
    private $createControllerPhpRenderer;

    private $indexControllerPhpRenderer;

    private $backButtonPhpRenderer;

    private $menu;

    private $menuDataFactory;

    private $fileHelper;

    private $domFactory;

    private $location;

    public function __construct(
        Crud\CreateControllerPhpRenderer $createControllerPhpRenderer,
        Crud\IndexControllerPhpRenderer $indexControllerPhpRenderer,
        Crud\BackButtonPhpRenderer $backButtonPhpRenderer,
        Menu $menu,
        Menu\DataFactory $menuDataFactory,
        FileHelper $fileHelper,
        DomFactory $domFactory,
        Location $location
    ) {
        $this->createControllerPhpRenderer = $createControllerPhpRenderer;
        $this->indexControllerPhpRenderer = $indexControllerPhpRenderer;
        $this->backButtonPhpRenderer = $backButtonPhpRenderer;
        $this->menu = $menu;
        $this->menuDataFactory = $menuDataFactory;
        $this->fileHelper = $fileHelper;
        $this->domFactory = $domFactory;
        $this->location = $location;
    }

    public function addToModule(DataInterface $data)
    {
        $this->createSaveController($data);
        $this->createDeleteController($data);
        $this->createFormUiComponent($data);
        $this->createGridUiComponent($data);
        $this->createDataProvider($data);
        $this->createActionsColumn($data);
        $this->createRoutesXml($data);
        $this->createAcl($data);
        $this->createMenu($data);
    }

    protected function createSaveController(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createDeleteController(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createCreateLayout(DataInterface $data)
    {
        /** @var Crud\Data $data */
        $fileName = $data->getModuleName()->getSlug([$data->getModel()->getVar(), 'create']).'.xml';
        $location = $data->getModuleName()->getPath(['view', 'adminhtml', 'layout', $fileName]);
        $uiComponentName = $data->getModuleName()->getSlug([$data->getModel()->getVar(), 'form', 'create']);
        $contents = $this->domFactory->create($location, Location::TYPE_LAYOUT)
            ->updateElement('update', 'handle', 'styles')
            ->updateElement('update', 'handle', 'editor')
            ->updateElement('body')
            ->updateElement('referenceContainer', 'name', 'content', null, ['body'])
            ->updateElement('uiComponent', 'name', $uiComponentName, null, ['body', 'referenceContainer[@name="content"]'])
            ->print();
        //$this->fileHelper->saveFile($location, $contents, true);
    }

    protected function createEditLayout(DataInterface $data)
    {
        /** @var Crud\Data $data */
        $fileName = $data->getModuleName()->getSlug([$data->getModel()->getVar(), 'edit']).'.xml';
        $location = $data->getModuleName()->getPath(['view', 'adminhtml', 'layout', $fileName]);
        $uiComponentName = $data->getModuleName()->getSlug([$data->getModel()->getVar(), 'form']);
        $contents = $this->domFactory->create($location, Location::TYPE_LAYOUT)
            ->updateElement('update', 'handle', 'styles')
            ->updateElement('update', 'handle', 'editor')
            ->updateElement('body')
            ->updateElement('referenceContainer', 'name', 'content', null, ['body'])
            ->updateElement('uiComponent', 'name', $uiComponentName, null, ['body', 'referenceContainer[@name="content"]'])
            ->print();
        //$this->fileHelper->saveFile($location, $contents, true);
    }

    protected function createIndexLayout(DataInterface $data)
    {
        /** @var Crud\Data $data */
        $fileName = $data->getModuleName()->getSlug([$data->getModel()->getVar(), 'index']).'.xml';
        $location = $data->getModuleName()->getPath(['view', 'adminhtml', 'layout', $fileName]);
        $uiComponentName = $data->getModuleName()->getSlug([$data->getModel()->getVar(), 'listing']);
        $contents = $this->domFactory->create($location, Location::TYPE_LAYOUT)
            ->updateElement('update', 'handle', 'styles')
            ->updateElement('body')
            ->updateElement('referenceContainer', 'name', 'content', null, ['body'])
            ->updateElement('uiComponent', 'name', $uiComponentName, null, ['body', 'referenceContainer[@name="content"]'])
            ->print();
        print_r($contents); die();
        //$this->fileHelper->saveFile($location, $contents, true);
    }

    protected function createFormUiComponent(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createGridUiComponent(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createDataProvider(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createActionsColumn(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createDiXml(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createRoutesXml(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createAcl(DataInterface $data)
    {
        /** @var Crud\Data $data */
    }

    protected function createMenu(DataInterface $data)
    {
//        /** @var Crud\Data $data */
//        $menuData = $this->menuDataFactory->create([
//            'moduleName' => $data->getModuleName(),
//            'menuName' => $data->getModuleName()->getSlug([$data->getModel()->getVar(), 'grid']),
//            'parentMenu' => $input->getOption('parent'),
//            'title' => $input->getOption('title'),
//            'sortOrder' => $input->getOption('sortOrder'),
//            'action' => $input->getOption('action'),
//            'resource' => $input->getOption('resource')
//        ]);
//        $this->menu->addToModule($menuData);
    }







    private function createPhpFile(Command\Data $data)
    {
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getPath(), $contents);
    }

    private function createXmlFile(Command\Data $data)
    {
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_DI, Location::AREA_GLOBAL);
        $contents = $this->domFactory->create($location, Location::TYPE_DI)
            ->updateElement('type', 'name', 'Magento\\Framework\\Console\\CommandList')
            ->updateElement('arguments', null, null, null, ['type[@name="Magento\\Framework\\Console\\CommandList"]'])
            ->updateElement('argument', 'name', 'commands', null, ['type[@name="Magento\\Framework\\Console\\CommandList"]', 'arguments'])
            ->updateElement('item', 'name', $data->getItemName(), $data->getInstance(), ['type[@name="Magento\\Framework\\Console\\CommandList"]', 'arguments', 'argument[@name="commands"]'])
            ->updateAttribute('xsi:type', 'object', ['type[@name="Magento\\Framework\\Console\\CommandList"]', 'arguments', 'argument[@name="commands"]', 'item[@name="'.$data->getItemName().'"]'])
            ->print();
        $this->fileHelper->saveFile($location, $contents, true);
    }
}