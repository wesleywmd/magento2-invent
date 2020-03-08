<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\Model;

class ControllerSavePhp extends AbstractControllerPhp implements RendererInterface
{
    protected $className = 'Save';

    protected $classExtends = 'AbstractController';

    protected $classImplements = 'HttpGetActionInterface';

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $data->getModel()->getInterfaceInstance(),
            $data->getModel()->getRepositoryInterfaceInstance(),
            $data->getModuleName()->getNamespace(['Model', $data->getModel()->getModelName(), 'DataProvider']),
            $data->getModel()->getInstance().'Factory',
            'Magento\Backend\App\Action\Context',
            'Magento\Framework\App\Request\DataPersistorInterface',
            'Magento\Framework\Exception\CouldNotSaveException'
        ];
    }

    protected function getClassStmts(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $this->getAdminResourceConstant($data->getMenu()->getMenuResource(), '_edit'),
            $this->phpBuilder->property($data->getModel()->getVar().'Factory')->makeProtected(),
            $this->phpBuilder->property($data->getModel()->getRepositoryVar())->makeProtected(),
            $this->phpBuilder->property('dataPersistor')->makeProtected(),
            $this->getConstructor($data->getModel()),
            $this->getExecuteMethod($data->getModel()),
            $this->getGetModelMethod($data->getModel()),
            $this->getGetSuccessMessageMethod($data->getModel()->getVar()),
            $this->getGetRedirectMethod($data->getModel()->getInterfaceName()),
            $this->getSendErrorMessageMethod()
        ];
    }

    private function getConstructor(Model\Data $model)
    {
        return $this->phpBuilder->constructor([
            'context' => 'Context',
            $model->getVar().'Factory' => $model->getModelName().'Factory',
            $model->getRepositoryVar() => $model->getModelName().'RepositoryInterface',
            'dataPersistor' => 'DataPersistorInterface'
        ], true, ['context']);
    }

    protected function getExecuteMethod(Model\Data $model)
    {
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmts([
                $this->phpBuilder->if($this->phpBuilder->booleanNot($this->thisGetRequestParams()), ['stmts' => [
                    $this->phpBuilder->returnStmt(
                        $this->phpBuilder->thisMethodCall('sendErrorMessage', [
                            $this->phpBuilder->translate('No Data Provided')
                        ])
                    )
                ]]),
                $this->phpBuilder->tryCatch([
                    $this->phpBuilder->assign($this->varEntityId(), $this->thisGetRequestParam(
                        $this->phpBuilder->classConstFetch($model->getInterfaceName(), 'ENTITY_ID'),
                        $this->phpBuilder->val(false)
                    )),
                    $this->phpBuilder->assign(
                        $this->varModel($model),
                        $this->phpBuilder->thisMethodCall('getModel', [$this->varEntityId()])
                    ),
                    $this->phpBuilder->methodCall($this->varModel($model), 'setData', [$this->thisGetRequestParams()]),
                    $this->phpBuilder->methodCall($this->thisRepository($model), 'save', [$this->varModel($model)])
                ], [
                    $this->phpBuilder->catch('CouldNotSaveException', $this->phpBuilder->var('exception'), [
                        $this->phpBuilder->returnStmt(
                            $this->phpBuilder->thisMethodCall('sendErrorMessage', [$this->phpBuilder->methodCall(
                                $this->phpBuilder->var('exception'),
                                'getMessage'
                            )])
                        )
                    ]),
                    $this->phpBuilder->catch('\Exception', $this->phpBuilder->var('exception'), [
                        $this->phpBuilder->returnStmt(
                            $this->phpBuilder->thisMethodCall('sendErrorMessage', [
                                $this->phpBuilder->translate('Something went wrong')
                            ])
                        )
                    ])
                ]),
                $this->phpBuilder->methodCall($this->thisMessageManager(), 'addSuccessMessage', [
                    $this->phpBuilder->thisMethodCall('getSuccessMessage', [$this->phpBuilder->var('entityId')])
                ]),
                $this->phpBuilder->returnStmt($this->phpBuilder->thisMethodCall('getRedirect', [
                    $this->phpBuilder->methodCall($this->varModel($model), 'getEntityId')
                ]))
            ]);
    }

    private function getGetModelMethod(Model\Data $model)
    {
        return $this->phpBuilder->method('getModel')
            ->makePrivate()
            ->addParam($this->phpBuilder->param('entityId'))
            ->addStmts([
                $this->phpBuilder->if($this->phpBuilder->var('entityId'), ['stmts' => [
                    $this->phpBuilder->returnStmt(
                        $this->phpBuilder->methodCall($this->thisRepository($model), 'get', [
                            $this->phpBuilder->var('entityId')
                        ])
                    )
                ]]),
                $this->phpBuilder->returnStmt($this->phpBuilder->methodCall(
                    $this->phpBuilder->thisPropertyFetch($model->getVar().'Factory'),
                    'create'
                ))
            ]);
    }

    private function getGetSuccessMessageMethod($modelName)
    {
        return $this->phpBuilder->method('getSuccessMessage')
            ->makePrivate()
            ->addStmts([
                $this->phpBuilder->if($this->phpBuilder->var('entityId'), ['stmts' => [
                    $this->phpBuilder->returnStmt(
                        $this->phpBuilder->translate('Successfully saved '.$modelName)
                    )
                ]]),
                $this->phpBuilder->returnStmt(
                    $this->phpBuilder->translate('Successfully created '.$modelName)
                )
            ]);
    }

    private function getGetRedirectMethod($interfaceName)
    {
        return $this->phpBuilder->method('getRedirect')
            ->makePrivate()
            ->addStmts([
                $this->assignResultRedirect(),
                $this->phpBuilder->if(
                    $this->phpBuilder->identical(
                        $this->thisGetRequestParam('back'),
                        $this->phpBuilder->val('edit')
                    ), ['stmts' => [
                        $this->returnResultRedirectGetPath('*/*/edit', [
                            $this->phpBuilder->arrayDefine([
                                $this->phpBuilder->arrayItem(
                                    $this->phpBuilder->var('entityId'),
                                    $this->phpBuilder->classConstFetch($interfaceName, 'ENTITY_ID')
                                )
                            ])
                        ])
                    ], 'elseifs' => [
                        $this->phpBuilder->elseif(
                            $this->thisGetRequestParam('redirect_to_new'),
                            [$this->returnResultRedirectGetPath('*/*/create')]
                        )
                    ]]
                ),
                $this->returnResultRedirectGetPath('*/*/')
            ]);
    }

    private function getSendErrorMessageMethod()
    {
        return $this->phpBuilder->method('sendErrorMessage')
            ->makePrivate()
            ->addParam($this->phpBuilder->param('message'))
            ->addStmts([
                $this->phpBuilder->methodCall(
                    $this->phpBuilder->thisPropertyFetch('dataPersistor'),
                    'set',
                    [
                        $this->phpBuilder->classConstFetch('DataProvider', 'PERSISTOR_KEY'),
                        $this->thisGetRequestParams()
                    ]
                ),
                $this->phpBuilder->methodCall($this->thisMessageManager(), 'addErrorMessage', [
                    $this->phpBuilder->var('message')
                ]),
                $this->assignResultRedirect(),
                $this->returnResultRedirectGetPath(
                    $this->phpBuilder->methodCall(
                        $this->phpBuilder->thisPropertyFetch('_redirect'),
                        'getRefererUrl'
                    )
                )
            ]);

    }
}