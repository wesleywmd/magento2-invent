<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\Model;

class ControllerEditPhp extends AbstractControllerPhp implements RendererInterface
{
    protected $className = 'Edit';

    protected $classExtends = 'AbstractController';

    protected $classImplements = 'HttpGetActionInterface';

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $data->getModel()->getRepositoryInterfaceInstance(),
            'Magento\Backend\App\Action\Context',
            'Magento\Backend\Model\View\Result\Redirect',
            'Magento\Framework\App\Action\HttpGetActionInterface',
            'Magento\Framework\Controller\ResultInterface',
            'Magento\Framework\Exception\NoSuchEntityException'
        ];
    }

    protected function getClassStmts(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $this->getAdminResourceConstant($data->getMenu()->getMenuResource(), '_edit'),
            $this->phpBuilder->property($data->getModel()->getRepositoryVar())->makeProtected(),
            $this->getConstructor($data->getModel()->getRepositoryVar(), $data->getModel()->getModelName()),
            $this->getExecuteMethod($data->getModel()),
            $this->getCreateTitleMethod($data->getModel())
        ];
    }

    private function getConstructor($repositoryVar, $modelName)
    {
        return $this->phpBuilder->constructor([
            'context' => 'Context',
            $repositoryVar => $modelName.'RepositoryInterface'
        ], true, ['context']);
    }

    protected function getExecuteMethod(Model\Data $model)
    {
        $titleTranslate = $this->phpBuilder->translate(
            $this->phpBuilder->thisMethodCall('createTitle', [$this->varModel($model)])
        );
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmts([
                $this->phpBuilder->assign($this->varEntityId(), $this->thisGetRequestParam('entity_id')),
                $this->phpBuilder->tryCatch([
                    $this->assignModel($model)
                ], [
                    $this->phpBuilder->catch('NoSuchEntityException', 'exception',
                        $this->redirectWithErrorMessage('This '.$model->getVar().' no longer exists.')
                    ),
                    $this->phpBuilder->catch('\Exception', 'exception',
                        $this->redirectWithErrorMessage('Something went wrong.')
                    )
                ]),
                $this->phpBuilder->thisMethodCall('initPage'),
                $this->phpBuilder->thisMethodCall('prependTitle', [$titleTranslate]),
                $this->phpBuilder->thisMethodCall('_addBreadcrumb', [$titleTranslate, $titleTranslate]),
                $this->phpBuilder->thisMethodCall('renderPage')
            ]);
    }

    private function getCreateTitleMethod(Model\Data $model)
    {
        return $this->phpBuilder->method('createTitle')
            ->addParam($this->phpBuilder->param($model->getVar()))
            ->addStmts([
                $this->phpBuilder->if($this->phpBuilder->methodCall($this->varModel($model), 'getEntityId'), [
                    'stmts' => [
                        $this->phpBuilder->returnStmt($this->phpBuilder->val('Edit '.$model->getModelName()))
                    ]
                ]),
                $this->phpBuilder->returnStmt($this->phpBuilder->val('New '.$model->getModelName()))
            ]);
    }

    private function redirectWithErrorMessage($message)
    {
        return [
            $this->thisMessageManagerCall('addErrorMessage', $message),
            $this->assignResultRedirect(),
            $this->returnResultRedirectGetPath('*/*/')
        ];
    }
}