<?php
namespace Wesleywmd\Invent\Model\Crud\Renderer;

use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;
use Wesleywmd\Invent\Model\Crud;

class ControllerAbstractPhp extends AbstractControllerPhp implements RendererInterface
{
    protected $className = 'AbstractController';

    protected $classExtends = 'Action';

    protected function getUseStatements(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return ['Magento\Backend\App\Action'];
    }

    protected function getClassStmts(DataInterface $data)
    {
        /** @var Crud\Data $data */
        return [
            $this->getAdminResourceConstant($data->getMenu()->getMenuResource()),
            $this->getInitPageMethod($data->getModel()->getModelName(), $data->getMenu()->getMenuResource()),
            $this->getPrependTitleMethod(),
            $this->getRenderPageMethod()
        ];
    }

    protected function getClassStatement(DataInterface $data)
    {
        /** @var \PhpParser\Builder\Class_ $class */
        $class = parent::getClassStatement($data);
        return $class->makeAbstract();
    }

    private function getInitPageMethod($modelName, $menuResource)
    {
        $translateModelName = $this->phpBuilder->translate($modelName);
        return $this->phpBuilder->method('initPage')
            ->makeProtected()
            ->addStmts([
                $this->phpBuilder->methodCall(
                    $this->phpBuilder->thisPropertyFetch('_view'),
                    'loadLayout'
                ),
                $this->phpBuilder->thisMethodCall('_setActiveMenu', [$this->phpBuilder->val($menuResource)]),
                $this->phpBuilder->thisMethodCall('_addBreadcrumb', [$translateModelName, $translateModelName]),
                $this->phpBuilder->thisMethodCall('prependTitle', [$translateModelName])
            ]);
    }

    private function getPrependTitleMethod()
    {
        return $this->phpBuilder->method('prependTitle')
            ->makeProtected()
            ->addParam($this->phpBuilder->param('title'))
            ->addStmt(
                $this->phpBuilder->methodCall(
                    $this->phpBuilder->methodCall(
                        $this->phpBuilder->methodCall(
                            $this->phpBuilder->methodCall(
                                $this->phpBuilder->thisPropertyFetch('_view'),
                                'getPage'
                            ), 'getConfig'
                        ), 'getTitle'
                    ), 'prepend',
                    [$this->phpBuilder->var('title')]
                )
            );
    }

    private function getRenderPageMethod()
    {
        return $this->phpBuilder->method('renderPage')
            ->makeProtected()
            ->addStmt(
                $this->phpBuilder->methodCall(
                    $this->phpBuilder->thisPropertyFetch('_view'),
                    'renderLayout'
                )
            );
    }
}