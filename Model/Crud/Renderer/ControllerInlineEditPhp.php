<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\Model;

class ControllerInlineEditPhp extends AbstractControllerPhp implements RendererInterface
{
    protected $className = 'InlineEdit';

    protected $classExtends = 'AbstractController';

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $data->getModel()->getInterfaceInstance(),
            $data->getModel()->getRepositoryInterfaceInstance(),
            'Magento\Backend\App\Action\Context',
            'Magento\Framework\Controller\Result\JsonFactory'
        ];
    }

    protected function getClassStmts(DataInterface $data)
    {
        return [
            $this->getAdminResourceConstant($data->getMenu()->getMenuResource(), '_edit'),
            $this->phpBuilder->property($data->getModel()->getRepositoryVar())->makeProtected(),
            $this->phpBuilder->property('jsonFactory')->makeProtected(),
            $this->getConstructor($data->getModel()->getRepositoryVar(), $data->getModel()->getModelName()),
            $this->getExecuteMethod($data->getModel()),
            $this->getResultMethod(),
            $this->getErrorWithItemIdMethod($data->getModel())
        ];
    }

    private function getConstructor($repositoryVar, $modelName)
    {
        return $this->phpBuilder->constructor([
            'context' => 'Context',
            $repositoryVar => $modelName.'RepositoryInterface',
            'jsonFactory' => 'JsonFactory'
        ], true, ['context']);
    }

    protected function getExecuteMethod(Model\Data $model)
    {
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmts([
                $this->phpBuilder->assignSimple('error',false),
                $this->phpBuilder->assignSimple('messages', []),
                $this->phpBuilder->if(
                    $this->phpBuilder->booleanNot($this->thisGetRequestParam('isAjax')), ['stmts' => [
                        $this->phpBuilder->returnStmt($this->phpBuilder->thisMethodCall('result'))
                    ]]
                ),
                $this->phpBuilder->assign(
                    $this->phpBuilder->var('items'),
                    $this->thisGetRequestParam('items', [])
                ),
                $this->phpBuilder->if(
                    $this->phpBuilder->booleanNot($this->phpBuilder->funcCall('count', [
                        $this->phpBuilder->var('items')
                    ])), ['stmts' => [
                        $this->phpBuilder->returnStmt($this->phpBuilder->thisMethodCall('result', [
                            $this->phpBuilder->translate('Please correct the data sent.'),
                            $this->phpBuilder->val(true)
                        ]))
                    ]]
                ),
                $this->phpBuilder->foreachLoop(
                    $this->phpBuilder->funcCall('array_keys', [$this->phpBuilder->var('items')]),
                    $this->phpBuilder->var($model->getIdVar()),
                    ['stmts' => [
                        $this->phpBuilder->tryCatch([
                            $this->phpBuilder->assign($this->varModel($model), $this->phpBuilder->methodCall(
                                $this->thisRepository($model),
                                'get',
                                [$this->phpBuilder->var($model->getIdVar())]
                            )),
                            $this->phpBuilder->methodCall($this->varModel($model), 'setData', [
                                $this->phpBuilder->funcCall('array_merge', [
                                    $this->phpBuilder->methodCall($this->varModel($model), 'getData'),
                                    $this->phpBuilder->arrayDimFetch($this->phpBuilder->var('items'), $this->phpBuilder->var($model->getIdVar()))
                                ])
                            ]),
                            $this->phpBuilder->methodCall($this->thisRepository($model), 'save', [$this->varModel($model)])
                        ], [
                            $this->phpBuilder->catch('\Exception', 'exception', [
                                $this->phpBuilder->assign(
                                    $this->phpBuilder->arrayDimFetch($this->phpBuilder->var('messages')),
                                    $this->phpBuilder->thisMethodCall('getErrorWithItemId', [
                                        $this->varModel($model),
                                        $this->phpBuilder->translate(
                                            $this->phpBuilder->methodCall(
                                                $this->phpBuilder->var('exception'),
                                                'getMessage'
                                            )
                                        )
                                    ])
                                ),
                                $this->phpBuilder->assign(
                                    $this->phpBuilder->var('error'),
                                    $this->phpBuilder->val(true)
                                )
                            ])
                        ]),
                    ]]
                ),
                $this->phpBuilder->returnStmt($this->phpBuilder->thisMethodCall('result', [
                    $this->phpBuilder->var('messages'),
                    $this->phpBuilder->var('error')
                ]))
            ]);
    }

    private function getResultMethod()
    {
        return $this->phpBuilder->method('result')
            ->addParams([
                $this->phpBuilder->param('messages')->setDefault([]),
                $this->phpBuilder->param('error')->setDefault(false)
            ])
            ->addStmts([
                $this->phpBuilder->assign($this->phpBuilder->var('resultJson'), $this->phpBuilder->methodCall(
                    $this->phpBuilder->thisPropertyFetch('jsonFactory'),
                    'create'
                )),
                $this->phpBuilder->returnStmt($this->phpBuilder->methodCall($this->phpBuilder->var('resultJson'), 'setData', [
                    $this->phpBuilder->arrayDefine([
                        'messages' => $this->phpBuilder->var('messages'),
                        'error' => $this->phpBuilder->var('error')
                    ])
                ]))
            ]);
    }

    private function getErrorWithItemIdMethod(Model\Data $model)
    {
        return $this->phpBuilder->method('getErrorWithItemId')
            ->addParams([
                $this->phpBuilder->param($model->getVar())->setType($model->getInterfaceName()),
                $this->phpBuilder->param('errorText')
            ])
            ->addStmts([
                $this->phpBuilder->returnStmt($this->phpBuilder->translate('[Lifestyle ID: %1] %2', [
                    $this->phpBuilder->methodCall($this->varModel($model), 'getEntityId'),
                    $this->phpBuilder->var('errorText')
                ]))
            ]);
    }
}