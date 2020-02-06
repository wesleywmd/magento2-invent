<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use PhpParser\Node\Expr\Array_;
use PhpParser\Node\Expr\ArrayDimFetch;
use PhpParser\Node\Stmt\Continue_;
use PhpParser\Node\Stmt\Foreach_;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Component\AbstractPhpRenderer;
use Wesleywmd\Invent\Model\PhpParser\PhpBuilder;
use Wesleywmd\Invent\Model\PhpParser\PrettyPrinter;

class ActionsColumnPhp extends AbstractPhpRenderer implements RendererInterface
{
    public function getPath(DataInterface $data)
    {
        return $data->getActionsColumnPath();
    }

    protected function getNamespace(DataInterface $data)
    {
        return $data->getActionsColumnNamespace();
    }

    protected function getUseStatements(DataInterface $data)
    {
        return [
            $data->getModuleName()->getNamespace(['Api', 'Data', $data->getModelName().'Interface']),
            'Creatuity\OptimumImages\Api\Data\ImageInterface',
            'Magento\Framework\UrlInterface',
            'Magento\Framework\View\Element\UiComponent\ContextInterface',
            'Magento\Framework\View\Element\UiComponentFactory',
            'Magento\Ui\Component\Listing\Columns\Column'
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        return $this->phpBuilder->class($data->getModelName().'Actions')
            ->extend('Column')
            ->addStmt($this->getConstructorMethod($data))
            ->addStmt($this->getPrepareDataSourceMethod($data));
    }

    protected function getConstructorMethod(DataInterface $data)
    {
        return $this->phpBuilder->method('__construct')->makePublic()
            ->addParams([
                $this->phpBuilder->param('context')->setType('ContextInterface'),
                $this->phpBuilder->param('uiComponentFactory')->setType('UiComponentFactory'),
                $this->phpBuilder->param('urlBuilder')->setType('UrlInterface'),
                $this->phpBuilder->param('components')->setType('array')->setDefault([]),
                $this->phpBuilder->param('data')->setType('array')->setDefault([])
            ])
            ->addStmt($this->phpBuilder->staticCall('parent', '__construct', [
                $this->phpBuilder->var('context'),
                $this->phpBuilder->var('uiComponentFactory'),
                $this->phpBuilder->var('components'),
                $this->phpBuilder->var('data')
            ]))
            ->addStmt($this->phpBuilder->assign(
                $this->phpBuilder->thisPropertyFetch('urlBuilder'),
                $this->phpBuilder->var('urlBuilder')
            ));
    }

    protected function getPrepareDataSourceMethod($data)
    {
        $dataItems = $this->phpBuilder->arrayMultiDimFetch($this->phpBuilder->var('dataSource'), [
            $this->phpBuilder->val('data'),
            $this->phpBuilder->val('items')
        ]);
        return $this->phpBuilder->method('prepareDataSource')->makePublic()
            ->addParam($this->phpBuilder->param('dataSource')->setType('array'))
            ->addStmt($this->phpBuilder->if(
                $this->phpBuilder->booleanNot($this->phpBuilder->funcCall('isset', [$dataItems,])), [
                    'stmts' => [$this->phpBuilder->returnStmt($this->phpBuilder->var('dataSource'))]
                ]
            ))
            ->addStmt(new Foreach_($dataItems, $this->phpBuilder->var('item'), [
                'byRef' => true, 'stmts' => [
                    $this->phpBuilder->if(
                        $this->phpBuilder->booleanNot(
                            $this->phpBuilder->funcCall('isset', [
                                $this->phpBuilder->arrayDimFetch(
                                    $this->phpBuilder->var('item'),
                                    $this->phpBuilder->val('entity_id')
                                )
                            ])
                        ), ['stmts' => [new Continue_()]]
                    ),
                    $this->phpBuilder->assign(
                        $this->phpBuilder->arrayMultiDimFetch($this->phpBuilder->var('item'), [
                            $this->phpBuilder->thisMethodCall('getData',[$this->phpBuilder->val('name')]), 
                            $this->phpBuilder->val('delete')
                        ]),
                        new Array_([])
                    )
                ]
            ]));
    }





//    public function prepareDataSource(array $dataSource)
//    {
//        if (!isset($dataSource['data']['items'])) {
//            return $dataSource;
//        }
//        foreach( $dataSource['data']['items'] as &$item ) {
//            if (!isset($item['entity_id'])) {
//                continue;
//            }
//            $item[$this->getData('name')]['delete'] = [
//                'href' => $this->urlBuilder->getUrl(
//                    'creatuity_optimumimages/images/delete',
//                    ['entity_id' => $item['entity_id']]
//                ),
//                'label' => __('Delete'),
//                'confirm' => [
//                    'title' => __('Delete %1', $item['key']),
//                    'message' => __('Are you sure you want to delete a %1 record?', $item['key'])
//                ],
//                'hidden' => false
//            ];
//        }
//        return $dataSource;
//    }
}