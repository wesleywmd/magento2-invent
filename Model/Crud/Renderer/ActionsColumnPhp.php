<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\Crud;

class ActionsColumnPhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getActionsColumnPath();
    }

    protected function getNamespace(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $data->getActionsColumnNamespace();
    }

    protected function getUseStatements(DataInterface $data)
    {
        return [
            'Magento\Framework\UrlInterface',
            'Magento\Framework\View\Element\UiComponent\ContextInterface',
            'Magento\Framework\View\Element\UiComponentFactory',
            'Magento\Ui\Component\Listing\Columns\Column'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return $this->phpBuilder->class($data->getModel()->getModelName().'Actions')
            ->extend('Column')
            ->addStmts([
                $this->getConstructorMethod(),
                $this->getPrepareDataSourceMethod(),
            ]);

    }

    protected function getConstructorMethod()
    {
        return $this->phpBuilder->method('__construct')
            ->makePublic()
            ->addParams([
                $this->phpBuilder->param('context')->setType('ContextInterface'),
                $this->phpBuilder->param('uiComponentFactory')->setType('UiComponentFactory'),
                $this->phpBuilder->param('urlBuilder')->setType('UrlInterface'),
                $this->phpBuilder->param('components')->setType('array')->setDefault([]),
                $this->phpBuilder->param('data')->setType('array')->setDefault([])
            ])
            ->addStmts([
                $this->phpBuilder->staticCall('parent', '__construct', [
                    $this->phpBuilder->var('context'),
                    $this->phpBuilder->var('uiComponentFactory'),
                    $this->phpBuilder->var('components'),
                    $this->phpBuilder->var('data')
                ]),
                $this->phpBuilder->assign(
                    $this->phpBuilder->thisPropertyFetch('urlBuilder'),
                    $this->phpBuilder->var('urlBuilder')
                )
            ]);
    }

    protected function getPrepareDataSourceMethod()
    {
        $dataSourceVar = $this->phpBuilder->var('dataSource');
        $itemVar = $this->phpBuilder->var('item');
        $dataItemsVar = $this->phpBuilder->arrayMultiDimFetch($dataSourceVar, [
            $this->phpBuilder->val('data'),
            $this->phpBuilder->val('items')
        ]);
        return $this->phpBuilder->method('prepareDataSource')
            ->makePublic()
            ->addParams([
                $this->phpBuilder->param('dataSource')->setType('array')
            ])
            ->addStmts([
                $this->phpBuilder->if(
                    $this->phpBuilder->booleanNot($this->phpBuilder->funcCall('isset', [$dataItemsVar])),
                    ['stmts' => [$this->phpBuilder->returnStmt($dataSourceVar)]]
                ),
                $this->phpBuilder->foreachLoop($dataItemsVar, $itemVar, [
                    'byRef' => true,
                    'stmts' => [
                        $this->phpBuilder->if(
                            $this->phpBuilder->booleanNot($this->phpBuilder->funcCall('isset', [
                                $this->phpBuilder->arrayDimFetch(
                                    $itemVar,
                                    $this->phpBuilder->val('entity_id')
                                )
                            ])),
                            ['stmts' => [$this->phpBuilder->continue()]]
                        ),
                        $this->phpBuilder->assign(
                            $this->phpBuilder->arrayMultiDimFetch($itemVar, [
                                $this->phpBuilder->thisMethodCall('getData', [$this->phpBuilder->val('name')]),
                                $this->phpBuilder->val('edit')
                            ]),
                            $this->phpBuilder->arrayDefine([
                                $this->phpBuilder->arrayItem($this->getUrlBuilderCall('*/*/edit'), $this->phpBuilder->val('href')),
                                $this->phpBuilder->arrayItem($this->phpBuilder->translate('Edit'), $this->phpBuilder->val('label')),
                                $this->phpBuilder->arrayItem($this->phpBuilder->val('false'), $this->phpBuilder->val('hidden')),
                            ])
                        ),
                        $this->phpBuilder->assign(
                            $this->phpBuilder->arrayMultiDimFetch($itemVar, [
                                $this->phpBuilder->thisMethodCall('getData', [$this->phpBuilder->val('name')]),
                                $this->phpBuilder->val('delete')
                            ]),
                            $this->phpBuilder->arrayDefine([
                                $this->phpBuilder->arrayItem($this->getUrlBuilderCall('*/*/delete'), $this->phpBuilder->val('href')),
                                $this->phpBuilder->arrayItem($this->phpBuilder->translate('Delete'), $this->phpBuilder->val('label')),
                                $this->phpBuilder->arrayItem($this->phpBuilder->arrayDefine([
                                    $this->phpBuilder->arrayItem($this->phpBuilder->translate('Delete %1', [
                                        $this->phpBuilder->arrayDimFetch($itemVar, $this->phpBuilder->val('label'))
                                    ]), $this->phpBuilder->val('title')),
                                    $this->phpBuilder->arrayItem($this->phpBuilder->translate('Are you sure you want to delete a %1 record?', [
                                        $this->phpBuilder->arrayDimFetch($itemVar, $this->phpBuilder->val('label'))
                                    ]), $this->phpBuilder->val('message'))
                                ]), $this->phpBuilder->val('confirm')),
                                $this->phpBuilder->arrayItem($this->phpBuilder->val('false'), $this->phpBuilder->val('hidden')),
                            ])
                        ),
                    ]
                ]),
                $this->phpBuilder->returnStmt($dataSourceVar)
            ]);
    }

    private function getUrlBuilderCall($url)
    {
        return $this->phpBuilder->methodCall(
            $this->phpBuilder->thisPropertyFetch('urlBuilder'),
            'getUrl', [
                $this->phpBuilder->val($url),
                $this->phpBuilder->arrayDefine([
                    $this->phpBuilder->arrayItem($this->phpBuilder->arrayDimFetch(
                        $this->phpBuilder->var('item'),
                        $this->phpBuilder->val('entity_id')
                    ))
                ])
            ]
        );
    }
}