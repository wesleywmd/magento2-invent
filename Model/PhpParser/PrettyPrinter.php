<?php
namespace Wesleywmd\Invent\Model\PhpParser;

use PhpParser\PrettyPrinter\Standard;

class PrettyPrinter extends Standard
{
    public function print(array $stmts) : string {
        $p = "<?php\n";
        
        if (!$stmts) {
            return $p;
        }

        $p .= $this->prettyPrint($stmts);

        if ($stmts[0] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/^<\?php\s+\?>\n?/', '', $p);
        }
        if ($stmts[count($stmts) - 1] instanceof Stmt\InlineHTML) {
            $p = preg_replace('/<\?php$/', '', rtrim($p));
        }

        return $p;
    }

    protected function pStmts(array $nodes, bool $indent = true) : string {
        if ($indent) {
            $this->indent();
        }

        $result = '';
        $first = true;
        foreach ($nodes as $node) {
            if( $this->isNorthSpaced($node) && !$first ) {
                $result .= $this->nl;
            }
            
            if( $first ) {
                $first = false;
            }
            
            $comments = $node->getComments();
            if ($comments) {
                $result .= $this->nl . $this->pComments($comments);
                if ($node instanceof Stmt\Nop) {
                    continue;
                }
            }

            $result .= $this->nl . $this->p($node);
        }

        if ($indent) {
            $this->outdent();
        }

        return $result;
    }
    
    protected function isNorthSpaced($node)
    {
        $classNames = ['\PhpParser\Node\Stmt\Class_', '\PhpParser\Node\Stmt\Property', '\PhpParser\Node\Stmt\ClassMethod'];
        foreach ($classNames as $className) {
            if (is_a($node, $className)) {
                return true;
            }
        }
        return false;
    }
}