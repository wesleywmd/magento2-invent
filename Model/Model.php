<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Model\Component\AbstractComponent;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Model extends AbstractComponent implements ComponentInterface
{
    private $interfacePhpRenderer;

    private $resourcePhpRenderer;

    private $collectionPhpRenderer;

    private $searchResultsInterfacePhpRenderer;

    private $repositoryInterfacePhpRenderer;

    private $repositoryPhpRenderer;

    private $preference;

    private $preferenceDataFactory;

    public function __construct(
        FileHelper $fileHelper,
        Model\PhpRenderer $phpRenderer,
        Model\InterfacePhpRenderer $interfacePhpRenderer,
        Model\ResourcePhpRenderer $resourcePhpRenderer,
        Model\CollectionPhpRenderer $collectionPhpRenderer,
        Model\SearchResultsInterfacePhpRenderer $searchResultsInterfacePhpRenderer,
        Model\RepositoryInterfacePhpRenderer $repositoryInterfacePhpRenderer,
        Model\RepositoryPhpRenderer $repositoryPhpRenderer,
        Model\XmlRenderer $xmlRenderer,
        Preference $preference,
        Preference\DataFactory $preferenceDataFactory
    ) {
        parent::__construct($fileHelper, $phpRenderer, $xmlRenderer);
        $this->interfacePhpRenderer = $interfacePhpRenderer;
        $this->resourcePhpRenderer = $resourcePhpRenderer;
        $this->collectionPhpRenderer = $collectionPhpRenderer;
        $this->searchResultsInterfacePhpRenderer = $searchResultsInterfacePhpRenderer;
        $this->repositoryInterfacePhpRenderer = $repositoryInterfacePhpRenderer;
        $this->repositoryPhpRenderer = $repositoryPhpRenderer;
        $this->preference = $preference;
        $this->preferenceDataFactory = $preferenceDataFactory;
    }

    public function addToModule(DataInterface $data)
    {
        /** @var Model\Data $data */
        $this->createPhpFile($data);
        $this->createInterfacePhpFile($data);
        $this->createResourceModelPhpFile($data);
        $this->createCollectionPhpFile($data);
        $this->createSearchResultsInterfacePhpFile($data);
        $this->createRepositoryInterfacePhpFile($data);
        $this->createRepositoryPhpFile($data);
        $this->createXmlFile($data);
        $this->createModelPreference($data);
        $this->createSearchResultsPreference($data);
        $this->createRepositoryPreference($data);
    }

    private function createInterfacePhpFile(DataInterface $data)
    {
        /** @var Model\Data $data */
        $contents = $this->interfacePhpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getInterfacePath(), $contents);
    }

    private function createResourceModelPhpFile(DataInterface $data)
    {
        /** @var Model\Data $data */
        $contents = $this->resourcePhpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getResourceModelPath(), $contents);
    }

    private function createCollectionPhpFile(DataInterface $data)
    {
        /** @var Model\Data $data */
        $contents = $this->collectionPhpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getCollectionPath(), $contents);
    }

    private function createSearchResultsInterfacePhpFile(DataInterface $data)
    {
        /** @var Model\Data $data */
        $contents = $this->searchResultsInterfacePhpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getSearchResultsInterfacePath(), $contents);
    }

    private function createRepositoryInterfacePhpFile(DataInterface $data)
    {
        /** @var Model\Data $data */
        $contents = $this->repositoryInterfacePhpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getRepositoryInterfacePath(), $contents);
    }

    private function createRepositoryPhpFile(DataInterface $data)
    {
        /** @var Model\Data $data */
        $contents = $this->repositoryPhpRenderer->getContents($data);
        $contents = str_replace('$this->resource->save($'.$data->getVar().')', '$this->resource->save($'.$data->getVar().');', $contents);
        $contents = str_replace('$this->resource->delete($'.$data->getVar().')', '$this->resource->delete($'.$data->getVar().');', $contents);
        $this->fileHelper->saveFile($data->getRepositoryPath(), $contents);
    }

    private function createPreference(ModuleName $moduleName, $for, $type)
    {
        $preferenceData = $this->preferenceDataFactory->create([
            'moduleName' => $moduleName,
            'for' => $for,
            'type' => $type,
            'area' => Location::AREA_GLOBAL
        ]);
        $this->preference->addToModule($preferenceData);
    }

    private function createModelPreference(DataInterface $data)
    {
        /** @var Model\Data $data */
        $this->createPreference($data->getModuleName(), $data->getInterfaceInstance(), $data->getInstance());
    }

    private function createSearchResultsPreference(DataInterface $data)
    {
        /** @var Model\Data $data */
        $this->createPreference($data->getModuleName(), $data->getSearchResultsInterfaceInstance(), 'Magento\Framework\Api\SearchResults');
    }

    private function createRepositoryPreference(DataInterface $data)
    {
        /** @var Model\Data $data */
        $this->createPreference($data->getModuleName(), $data->getRepositoryInterfaceInstance(), $data->getRepositoryInstance());
    }
}