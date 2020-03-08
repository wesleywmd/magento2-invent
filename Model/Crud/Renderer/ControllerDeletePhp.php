<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\Model;

class ControllerDeletePhp extends AbstractControllerPhp implements RendererInterface
{
    protected $className = 'Delete';

    protected $classExtends = 'AbstractController';

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $data->getModel()->getInterfaceInstance(),
            $data->getModel()->getRepositoryInterfaceInstance(),
            'Magento\Backend\App\Action\Context',
            'Magento\Backend\Model\View\Result\Redirect'
        ];
    }

    protected function getClassStmts(DataInterface $data)
    {
        return [
            $this->getAdminResourceConstant($data->getMenu()->getMenuResource(), '_delete'),
            $this->phpBuilder->property($data->getModel()->getRepositoryVar())->makeProtected(),
            $this->getConstructor($data->getModel()->getRepositoryVar(), $data->getModel()->getModelName()),
            $this->getExecuteMethod($data->getModel())
        ];
    }

    private function getConstructor($repositoryVar, $modelName)
    {
        return $this->phpBuilder->constructor([
            'context' => 'Context',
            $repositoryVar => $modelName.'RepositoryInterface'
        ], true, ['context']);
    }

    private function getExecuteMethod(Model\Data $model)
    {
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmts([
                $this->phpBuilder->tryCatch([
                    $this->phpBuilder->assign($this->varEntityId(), $this->thisGetRequestParam('entity_id')),
                    $this->assignResultRedirect(),
                    $this->assignModel($model),
                    $this->phpBuilder->methodCall($this->thisRepository($model), 'delete', [$this->varModel($model)]),
                    $this->thisMessageManagerCall('addSuccessMessage', $model->getModelName().' "%1" was deleted', [$this->varEntityId()])
                ], [
                    $this->phpBuilder->catch('\Exception', 'exception', [
                        $this->thisMessageManagerCall(
                            'addErrorMessage',
                            'There was an error deleting "%1" '.$model->getModelName(),
                            [$this->varEntityId()]
                        )
                    ])
                ]),
                $this->returnResultRedirectGetPath('*/*/')
            ]);
    }
}