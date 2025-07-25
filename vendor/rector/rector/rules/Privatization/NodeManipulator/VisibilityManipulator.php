<?php

declare (strict_types=1);
namespace Rector\Privatization\NodeManipulator;

use PhpParser\Modifiers;
use PhpParser\Node\Param;
use PhpParser\Node\Stmt\Class_;
use PhpParser\Node\Stmt\ClassConst;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\Stmt\Property;
use Rector\ValueObject\Visibility;
use RectorPrefix202507\Webmozart\Assert\Assert;
/**
 * @see \Rector\Tests\Privatization\NodeManipulator\VisibilityManipulatorTest
 */
final class VisibilityManipulator
{
    /**
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Param $node
     */
    public function hasVisibility($node, int $visibility) : bool
    {
        return (bool) ($node->flags & $visibility);
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Param $node
     */
    public function makeStatic($node) : void
    {
        $this->addVisibilityFlag($node, Visibility::STATIC);
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property $node
     */
    public function makeNonStatic($node) : void
    {
        if (!$node->isStatic()) {
            return;
        }
        $node->flags -= Modifiers::STATIC;
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Class_ $node
     */
    public function makeNonAbstract($node) : void
    {
        if (!$node->isAbstract()) {
            return;
        }
        $node->flags -= Modifiers::ABSTRACT;
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function makeFinal($node) : void
    {
        $this->addVisibilityFlag($node, Visibility::FINAL);
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod $node
     */
    public function makeNonFinal($node) : void
    {
        if (!$node->isFinal()) {
            return;
        }
        $node->flags -= Modifiers::FINAL;
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function changeNodeVisibility($node, int $visibility) : void
    {
        Assert::oneOf($visibility, [Visibility::PUBLIC, Visibility::PROTECTED, Visibility::PRIVATE, Visibility::STATIC, Visibility::ABSTRACT, Visibility::FINAL]);
        $this->replaceVisibilityFlag($node, $visibility);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Param $node
     */
    public function makePublic($node) : void
    {
        $this->replaceVisibilityFlag($node, Visibility::PUBLIC);
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function makeProtected($node) : void
    {
        $this->replaceVisibilityFlag($node, Visibility::PROTECTED);
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Param $node
     */
    public function makePrivate($node) : void
    {
        $this->replaceVisibilityFlag($node, Visibility::PRIVATE);
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassConst $node
     */
    public function removeFinal($node) : void
    {
        $node->flags -= Modifiers::FINAL;
    }
    /**
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Param $node
     */
    public function makeReadonly($node) : void
    {
        $this->addVisibilityFlag($node, Visibility::READONLY);
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Param $node
     */
    public function isReadonly($node) : bool
    {
        return $this->hasVisibility($node, Visibility::READONLY);
    }
    /**
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Param $node
     */
    public function removeReadonly($node) : void
    {
        $isConstructorPromotionBefore = $node instanceof Param && $node->isPromoted();
        $node->flags &= ~Modifiers::READONLY;
        $isConstructorPromotionAfter = $node instanceof Param && $node->isPromoted();
        if ($node instanceof Param && $isConstructorPromotionBefore && !$isConstructorPromotionAfter) {
            $this->makePublic($node);
        }
        if ($node instanceof Property) {
            $this->publicize($node);
        }
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property $node
     * @return \PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|null
     */
    public function publicize($node)
    {
        // already non-public
        if (!$node->isPublic()) {
            return null;
        }
        // explicitly public
        if ($this->hasVisibility($node, Visibility::PUBLIC)) {
            return null;
        }
        $this->makePublic($node);
        return $node;
    }
    /**
     * This way "abstract", "static", "final" are kept
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Param $node
     */
    private function removeVisibility($node) : void
    {
        // no modifier
        if ($node->flags === 0) {
            return;
        }
        if ($node instanceof Param) {
            $node->flags = 0;
            return;
        }
        if ($node->isPublic()) {
            $node->flags |= Modifiers::PUBLIC;
            $node->flags -= Modifiers::PUBLIC;
        }
        if ($node->isProtected()) {
            $node->flags -= Modifiers::PROTECTED;
        }
        if ($node->isPrivate()) {
            $node->flags -= Modifiers::PRIVATE;
        }
    }
    /**
     * @api
     * @param \PhpParser\Node\Stmt\Class_|\PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Param $node
     */
    private function addVisibilityFlag($node, int $visibility) : void
    {
        $node->flags |= $visibility;
    }
    /**
     * @param \PhpParser\Node\Stmt\ClassMethod|\PhpParser\Node\Stmt\Property|\PhpParser\Node\Stmt\ClassConst|\PhpParser\Node\Param $node
     */
    private function replaceVisibilityFlag($node, int $visibility) : void
    {
        $isStatic = $node instanceof ClassMethod && $node->isStatic();
        if ($isStatic) {
            $this->makeNonStatic($node);
        }
        if ($visibility !== Visibility::STATIC && $visibility !== Visibility::ABSTRACT && $visibility !== Visibility::FINAL) {
            $this->removeVisibility($node);
        }
        $this->addVisibilityFlag($node, $visibility);
        if ($isStatic) {
            $this->makeStatic($node);
        }
    }
}
