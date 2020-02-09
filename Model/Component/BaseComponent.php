<?php
namespace Wesleywmd\Invent\Model\Component;

use Wesleywmd\Invent\Api\ComponentInterface;
use Wesleywmd\Invent\Api\DataInterface;
use Wesleywmd\Invent\Api\RendererInterface;

class BaseComponent implements ComponentInterface
{
    private $renderers;

    public function __construct(
        array $renderers = []
    ) {
        $this->renderers = $renderers;
    }

    public function addToModule(DataInterface $data)
    {
        $this->checkRenderersCanCreate($data);
        foreach ($this->renderers as $renderer) {
            $location = $renderer->getPath($data);
            $dirname = dirname($location);
            if (!is_dir($dirname)) {
                mkdir($dirname, 0777, true);
            }
            file_put_contents($location, $renderer->getContents($data), LOCK_EX);
        }
    }

    private function checkRenderersCanCreate(DataInterface $data)
    {
        foreach ($this->renderers as $renderer) {
            if (!is_a($renderer, RendererInterface::class)) {
                throw new \Exception(get_class($renderer) .' does not implement '. RendererInterface::class);
            }
            $location = $renderer->getPath($data);
            if (is_file($location) && !$renderer->getForce()) {
                throw new \Exception('File already exists at: '.$location);
            }
        }
    }
}