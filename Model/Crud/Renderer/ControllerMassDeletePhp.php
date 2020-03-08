<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Crud;
use Wesleywmd\Invent\Model\Model;

class ControllerMassDeletePhp extends AbstractControllerPhp implements RendererInterface
{
    protected $className = 'MassDelete';

    protected $classExtends = 'AbstractController';

    protected $classImplements = 'HttpGetActionInterface';

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $data->getModel()->getResourceModelName() => $data->getModel()->getResourceModelInstance(),
            $data->getModel()->getCollectionFactoryInstance(),
            'Exception',
            'Magento\Backend\App\Action\Context',
            'Magento\Backend\Model\View\Result\Redirect',
            'Magento\Framework\App\Action\HttpPostActionInterface',
            'Magento\Framework\Controller\ResultFactory',
            'Magento\Framework\Exception\LocalizedException',
            'Magento\Ui\Component\MassAction\Filter'
        ];
    }

    protected function getClassStmts(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $this->getAdminResourceConstant($data->getMenu()->getMenuResource(), '_delete'),
            $this->phpBuilder->property('filter')->makeProtected(),
            $this->phpBuilder->property('collectionFactory')->makeProtected(),
            $this->phpBuilder->property(lcfirst($data->getModel()->getResourceModelName()))->makeProtected(),
            $this->getConstructor($data->getModel()->getResourceModelName()),
            $this->getExecuteMethod($data->getModel())
        ];
    }

    private function getConstructor($resourceModelName)
    {
        return $this->phpBuilder->constructor([
            'context' => 'Context',
            'filter' => 'Filter',
            'collectionFactory', 'CollectionFactory',
            lcfirst($resourceModelName) => $resourceModelName
        ], true, ['context']);
    }

    protected function getExecuteMethod(Model\Data $model)
    {
        return $this->phpBuilder->method('execute')
            ->makePublic()
            ->addStmts([
                $this->phpBuilder->assign(
                    $this->phpBuilder->var('collection'),
                    $this->phpBuilder->methodCall(
                        $this->phpBuilder->thisPropertyFetch('filter'),
                        'getCollection',
                        [
                            $this->phpBuilder->MethodCall(
                                $this->phpBuilder->thisPropertyFetch('collectionFactory'),
                                'create'
                            )
                        ]
                    )
                ),
                $this->phpBuilder->assign(
                    $this->phpBuilder->var('collectionSize'),
                    $this->phpBuilder->methodCall($this->phpBuilder->var('collection'), 'getSize')
                ),
                $this->phpBuilder->foreachLoop(
                    $this->phpBuilder->var('collection'),
                    $this->varModel($model),
                    [
                        'stmts' => [
                            $this->phpBuilder->methodCall(
                                $this->phpBuilder->thisPropertyFetch(lcfirst($model->getResourceModelName())),
                                'delete',
                                [$this->varModel($model)]
                            )
                        ]
                    ]
                ),
                $this->thisMessageManagerCall(
                    'addSuccessMessage',
                    'A total of %1 record(s) have been deleted.',
                    [$this->phpBuilder->var('collectionSize')]
                ),
                $this->assignResultRedirect(),
                $this->returnResultRedirectGetPath('*/*/')
            ]);
    }


}