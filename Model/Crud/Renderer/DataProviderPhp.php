<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\Model;

class DataProviderPhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getModelDataProviderPath();
    }

    public function getNamespace(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getModelDataProviderNamespace();
    }

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $data->getModel()->getInterfaceInstance(),
            $data->getModel()->getCollectionFactoryInstance(),
            'Magento\Framework\App\Request\DataPersistorInterface',
            'Magento\Ui\DataProvider\AbstractDataProvider'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $this->phpBuilder->class('DataProvider')
            ->extend('AbstractDataProvider')
            ->addStmts([
                $this->phpBuilder->const('PERSISTOR_KEY', $data->getPersistorKey()),
                $this->phpBuilder->property('collection')->makeProtected(),
                $this->phpBuilder->property('dataPersistor')->makeProtected(),
                $this->phpBuilder->property('loadedData')->makeProtected(),
                $this->getConstructor(),
                $this->getGetData($data->getModel())
            ]);
    }

    private function getConstructor()
    {
        return $this->phpBuilder->method('__construct')
            ->makePublic()
            ->addParams([
                $this->phpBuilder->param('name'),
                $this->phpBuilder->param('primaryFieldName'),
                $this->phpBuilder->param('requestFieldName'),
                $this->phpBuilder->param('collectionFactory')->setType('CollectionFactory'),
                $this->phpBuilder->param('dataPersistor')->setType('DataPersistorInterface'),
                $this->phpBuilder->param('meta')->setType('array')->setDefault([]),
                $this->phpBuilder->param('data')->setType('array')->setDefault([]),
            ])->addStmts([
                $this->phpBuilder->assign(
                    $this->phpBuilder->thisPropertyFetch('collection'),
                    $this->phpBuilder->methodCall($this->phpBuilder->var('collectionFactory'), 'create')
                ),
                $this->phpBuilder->staticCall('parent', '__construct', [
                    $this->phpBuilder->var('name'),
                    $this->phpBuilder->var('primaryFieldName'),
                    $this->phpBuilder->var('requestFieldName'),
                    $this->phpBuilder->var('meta'),
                    $this->phpBuilder->var('data'),
                ]),
                $this->phpBuilder->assign(
                    $this->phpBuilder->thisPropertyFetch('dataPersistor'),
                    $this->phpBuilder->var('dataPersistor')
                )
            ]);
    }

    protected function getGetData(Model\Data $model)
    {
        return $this->phpBuilder->method('getData')
            ->makePublic()
            ->addStmts([
                $this->phpBuilder->if(
                    $this->phpBuilder->funcCall('isset', [
                        $this->phpBuilder->thisPropertyFetch('loadedData')
                    ]),
                    ['stmts' => [
                        $this->phpBuilder->returnStmt($this->phpBuilder->thisPropertyFetch('loadedData'))
                    ]]
                ),
                $this->phpBuilder->assign(
                    $this->phpBuilder->var($model->getVar().'collection'),
                    $this->phpBuilder->methodCall($this->phpBuilder->thisPropertyFetch('collection'), 'getItems')
                ),
                $this->phpBuilder->foreachLoop(
                    $this->phpBuilder->var($model->getVar().'collection'),
                    $this->phpBuilder->var($model->getVar()),
                    ['stmts' => [
                        $this->phpBuilder->assign(
                            $this->phpBuilder->arrayDimFetch(
                                $this->phpBuilder->thisPropertyFetch('loadedData'),
                                $this->phpBuilder->methodCall($this->phpBuilder->var($model->getVar()), 'getEntityId')
                            ),
                            $this->phpBuilder->methodCall($this->phpBuilder->var($model->getVar()), 'getData')
                        )
                    ]]
                ),
                $this->phpBuilder->assign(
                    $this->phpBuilder->var('data'),
                    $this->phpBuilder->methodCall(
                        $this->phpBuilder->thisPropertyFetch('dataPersistor'),
                        'get',
                        [$this->phpBuilder->staticCall('self', 'PERSISTOR_KEY')]
                    )
                ),
                $this->phpBuilder->if(
                    $this->phpBuilder->booleanNot(
                        $this->phpBuilder->funcCall('empty', [$this->phpBuilder->var('data')])
                    ), ['stmts' => [
                        $this->phpBuilder->assign(
                            $this->phpBuilder->var($model->getVar()),
                            $this->phpBuilder->methodCall(
                                $this->phpBuilder->thisPropertyFetch('collection'),
                                'getNewEmptyItem'
                            )
                        ),
                        $this->phpBuilder->methodCall(
                            $this->phpBuilder->var($model->getVar()),
                            'setData',
                            [$this->phpBuilder->var('data')]
                        ),
                        $this->phpBuilder->assign(
                            $this->phpBuilder->arrayDimFetch(
                                $this->phpBuilder->thisPropertyFetch('loadedData'),
                                $this->phpBuilder->methodCall($this->phpBuilder->var($model->getVar()), 'getEntityId')
                            ),
                            $this->phpBuilder->methodCall($this->phpBuilder->var($model->getVar()), 'getData')
                        ),
                        $this->phpBuilder->methodCall(
                            $this->phpBuilder->thisPropertyFetch('dataPersistor'),
                            'clear',
                            [$this->phpBuilder->staticCall('self', 'PERSISTOR_KEY')]
                        )
                    ]]
                ),
                $this->phpBuilder->returnStmt($this->phpBuilder->thisPropertyFetch('loadedData'))
            ]);
    }
}