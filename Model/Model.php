<?php
namespace Wesleywmd\Invent\Model;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Helper\FileHelper;
use Wesleywmd\Invent\Helper\PathHelper;
use Wesleywmd\Invent\Model\Model\InterfacePhpRenderer;
use Wesleywmd\Invent\Model\XmlParser\DomFactory;
use Wesleywmd\Invent\Model\XmlParser\Location;

class Model implements ComponentInterface
{
    private $phpRenderer;

    private $interfacePhpRenderer;

    private $resourcePhpRenderer;

    private $collectionPhpRenderer;

    private $searchResultsInterfacePhpRenderer;

    private $repositoryInterfacePhpRenderer;

    private $repositoryPhpRenderer;

    private $preference;

    private $preferenceDataFactory;

    private $fileHelper;

    private $pathHelper;

    private $domFactory;

    private $location;

    public function __construct(
        Model\PhpRenderer $phpRenderer,
        Model\InterfacePhpRenderer $interfacePhpRenderer,
        Model\ResourcePhpRenderer $resourcePhpRenderer,
        Model\CollectionPhpRenderer $collectionPhpRenderer,
        Model\SearchResultsInterfacePhpRenderer $searchResultsInterfacePhpRenderer,
        Model\RepositoryInterfacePhpRenderer $repositoryInterfacePhpRenderer,
        Model\RepositoryPhpRenderer $repositoryPhpRenderer,
        Preference $preference,
        Preference\DataFactory $preferenceDataFactory,
        FileHelper $fileHelper,
        PathHelper $pathHelper,
        DomFactory $domFactory,
        Location $location
    ) {
        $this->phpRenderer = $phpRenderer;
        $this->interfacePhpRenderer = $interfacePhpRenderer;
        $this->resourcePhpRenderer = $resourcePhpRenderer;
        $this->collectionPhpRenderer = $collectionPhpRenderer;
        $this->searchResultsInterfacePhpRenderer = $searchResultsInterfacePhpRenderer;
        $this->repositoryInterfacePhpRenderer = $repositoryInterfacePhpRenderer;
        $this->repositoryPhpRenderer = $repositoryPhpRenderer;
        $this->preference = $preference;
        $this->preferenceDataFactory = $preferenceDataFactory;
        $this->fileHelper = $fileHelper;
        $this->pathHelper = $pathHelper;
        $this->domFactory = $domFactory;
        $this->location = $location;
    }

    public function addToModule(DataInterface $data)
    {
        if (!$this->pathHelper->fullPathExists($data->getModuleName())) {
            throw new \Exception('Module does not exist');
        }
        $this->createInterfacePhpFile($data);
        $this->createModelPhpFile($data);
        $this->createModelResourcePhpFile($data);
        $this->createCollectionPhpFile($data);
        $this->createSearchResultsInterfacePhpFile($data);
        $this->createRepositoryInterfacePhpFile($data);
        $this->createRepositoryPhpFile($data);
        $this->createDiXmlFile($data);
        $this->createSchemaXmlFile($data);
    }

    private function createInterfacePhpFile(Model\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), ['Api','Data', $data->getModelName().'Interface.php']);
        $contents = $this->interfacePhpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);
    }

    private function createModelPhpFile(Model\Data $data)
    {
        $contents = $this->phpRenderer->getContents($data);
        $this->fileHelper->saveFile($data->getPath(), $contents);
    }

    private function createModelResourcePhpFile(Model\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), ['Model', 'ResourceModel', $data->getModelName().'.php']);
        $contents = $this->resourcePhpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);
    }

    private function createCollectionPhpFile(Model\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), ['Model', 'ResourceModel', $data->getModelName(), 'Collection.php']);
        $contents = $this->collectionPhpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);
    }

    private function createSearchResultsInterfacePhpFile(Model\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), ['Api', 'Data', $data->getModelName().'SearchResultsInterface.php']);
        $contents = $this->searchResultsInterfacePhpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);
    }

    private function createRepositoryInterfacePhpFile(Model\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), ['Api', $data->getModelName().'RepositoryInterface.php']);
        $contents = $this->repositoryInterfacePhpRenderer->getContents($data);
        $this->fileHelper->saveFile($location, $contents);
    }

    private function createRepositoryPhpFile(Model\Data $data)
    {
        $location = $this->pathHelper->fullPath($data->getModuleName(), ['Model', $data->getModelName().'Repository.php']);
        $contents = $this->repositoryPhpRenderer->getContents($data);
        $contents = str_replace('$this->resource->save($'.$data->getModelVarName().')', '$this->resource->save($'.$data->getModelVarName().');', $contents);
        $contents = str_replace('$this->resource->delete($'.$data->getModelVarName().')', '$this->resource->delete($'.$data->getModelVarName().');', $contents);
        $this->fileHelper->saveFile($location, $contents);
    }

    private function createDiXmlFile(Model\Data $data)
    {
        $modelPreferenceData = $this->preferenceDataFactory->create([
            'moduleName' => $data->getModuleName(),
            'for' => $data->getModuleName()->getNamespace(['Api','Data',$data->getModelName().'Interface']),
            'type' => $data->getModuleName()->getNamespace(['Model',$data->getModelName()]),
            'area' => Location::AREA_GLOBAL
        ]);
        $this->preference->addToModule($modelPreferenceData);

        $searchResultsPreferenceData = $this->preferenceDataFactory->create([
            'moduleName' => $data->getModuleName(),
            'for' => $data->getModuleName()->getNamespace(['Api','Data',$data->getModelName().'SearchResultsInterface']),
            'type' => 'Magento\Framework\Api\SearchResults',
            'area' => Location::AREA_GLOBAL
        ]);
        $this->preference->addToModule($searchResultsPreferenceData);

        $repositoryPreferenceData = $this->preferenceDataFactory->create([
            'moduleName' => $data->getModuleName(),
            'for' => $data->getModuleName()->getNamespace(['Api',$data->getModelName().'RepositoryInterface']),
            'type' => $data->getModuleName()->getNamespace(['Model',$data->getModelName().'Repository']),
            'area' => Location::AREA_GLOBAL
        ]);
        $this->preference->addToModule($repositoryPreferenceData);
    }

    private function createSchemaXmlFile(Model\Data $data)
    {
        $location = $this->location->getPath($data->getModuleName(), Location::TYPE_DB_SCHEMA, Location::AREA_GLOBAL);
        $dom = $this->domFactory->create($location, Location::TYPE_DB_SCHEMA)
            ->updateElement('table', 'name', $data->getTableName())
            ->updateAttributes([
                'resource' => 'default',
                'engine' => 'innodb',
                'comment' => str_replace('\\', ' ', $data->getModuleName()->getNamespace([$data->getModelName()]))
            ], ['table[@name="'.$data->getTableName().'"]']);

        if (!$data->getNoEntityId()) {
            $dom->updateElement('column', 'name', 'entity_id', null, ['table[@name="'.$data->getTableName().'"]'])
                ->updateAttributes([
                    'xsi:type' => 'smallint',
                    'padding' => '6',
                    'unsigned' => 'false',
                    'nullable' => 'false',
                    'identity' => 'true',
                    'comment' => 'Entity ID'
                ], ['table[@name="'.$data->getTableName().'"]', 'column[@name="entity_id"]']);
        }

        foreach ($data->getColumns() as $column) {
            $dom->updateElement('column', 'name', $column, null, ['table[@name="'.$data->getTableName().'"]'])
                ->updateAttributes([
                    'xsi:type' => 'varchar',
                    'length' => '32',
                    'nullable' => 'false',
                    'comment' => implode(' ', array_map( function($piece) { return ucfirst($piece); }, explode('_', $column))),
                ], ['table[@name="'.$data->getTableName().'"]', 'column[@name="'.$column.'"]']);
        }

        if (!$data->getNoCreatedAt()) {
            $dom->updateElement('column', 'name', 'created_at', null, ['table[@name="'.$data->getTableName().'"]'])
                ->updateAttributes([
                    'xsi:type' => 'timestamp',
                    'on_update' => 'false',
                    'nullable' => 'false',
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => 'Created At'
                ], ['table[@name="'.$data->getTableName().'"]', 'column[@name="created_at"]']);
        }

        if (!$data->getNoUpdatedAt()) {
            $dom->updateElement('column', 'name', 'updated_at', null, ['table[@name="'.$data->getTableName().'"]'])
                ->updateAttributes([
                    'xsi:type' => 'timestamp',
                    'on_update' => 'true',
                    'nullable' => 'false',
                    'default' => 'CURRENT_TIMESTAMP',
                    'comment' => 'Updated At'
                ], ['table[@name="'.$data->getTableName().'"]', 'column[@name="updated_at"]']);
        }

        if (!$data->getNoEntityId()) {
            $dom->updateElement('constraint', 'referenceId', 'PRIMARY', null, ['table[@name="' . $data->getTableName() . '"]'])
                ->updateAttribute('xsi:type', 'primary', ['table[@name="' . $data->getTableName() . '"]', 'constraint[@referenceId="PRIMARY"]'])
                ->updateElement('column', 'name', 'entity_id', null, ['table[@name="' . $data->getTableName() . '"]', 'constraint[@referenceId="PRIMARY"]']);
        }
        
        $contents = $dom->print();
        $this->fileHelper->saveFile($location, $contents, true);
    }
}