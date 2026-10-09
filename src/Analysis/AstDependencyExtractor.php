<?php

declare(strict_types=1);

namespace ArchitectureGuard\Analysis;

use ArchitectureGuard\Contracts\DependencyExtractor;
use ArchitectureGuard\Core\Dependency;
use ArchitectureGuard\Core\SourceStructure;
use ArchitectureGuard\Exceptions\AnalysisException;
use PhpParser\Error;
use PhpParser\Node;
use PhpParser\Node\Name;
use PhpParser\Node\Stmt\ClassLike;
use PhpParser\NodeTraverser;
use PhpParser\NodeVisitor\NameResolver;
use PhpParser\NodeVisitorAbstract;
use PhpParser\ParserFactory;

final class AstDependencyExtractor implements DependencyExtractor
{
    /** @return list<SourceStructure> */
    public function extract(string $file): array
    {
        $code = @file_get_contents($file);
        if ($code === false) {
            throw new AnalysisException("Unable to read {$file}.");
        }
        try {
            $nodes = (new ParserFactory)->createForNewestSupportedVersion()->parse($code) ?? [];
        } catch (Error $error) {
            throw new AnalysisException("Unable to parse {$file}: {$error->getMessage()}", previous: $error);
        }
        $visitor = new class($file) extends NodeVisitorAbstract
        {
            /** @var list<SourceStructure> */
            public array $structures = [];

            private ?ClassLike $current = null;

            /** @var array<string, Dependency> */
            private array $dependencies = [];

            public function __construct(private readonly string $file) {}

            public function enterNode(Node $node): null
            {
                if ($node instanceof ClassLike && $node->name !== null) {
                    $this->current = $node;
                    $this->dependencies = [];
                }
                if ($this->current !== null && $node instanceof Name) {
                    $resolved = $node->getAttribute('resolvedName');
                    if ($resolved instanceof Name) {
                        $name = $resolved->toString();
                        $self = $this->current->namespacedName?->toString();
                        if ($name !== $self) {
                            $this->dependencies[$name.'@'.$node->getStartLine()] = new Dependency($name, $this->type($node), $node->getStartLine());
                        }
                    }
                }

                return null;
            }

            public function leaveNode(Node $node): null
            {
                if ($node === $this->current) {
                    $name = $node->namespacedName?->toString() ?? $node->name?->toString() ?? '';
                    $parts = explode('\\', $name);
                    array_pop($parts);
                    $kind = $node instanceof Node\Stmt\Interface_ ? 'interface' : ($node instanceof Node\Stmt\Trait_ ? 'trait' : ($node instanceof Node\Stmt\Enum_ ? 'enum' : 'class'));
                    $this->structures[] = new SourceStructure($name, implode('\\', $parts), $this->file, $node->getStartLine(), $kind, array_values($this->dependencies));
                    $this->current = null;
                }

                return null;
            }

            private function type(Name $name): string
            {
                $parent = $name->getAttribute('parent');

                return $parent instanceof Node\Expr\New_ ? 'instantiation' : ($parent instanceof Node\Expr\StaticCall || $parent instanceof Node\Expr\ClassConstFetch ? 'static_reference' : 'type_or_inheritance');
            }
        };
        $traverser = new NodeTraverser;
        $traverser->addVisitor(new NameResolver(null, ['replaceNodes' => false]));
        $traverser->addVisitor(new class extends NodeVisitorAbstract
        {
            public function enterNode(Node $node): null
            {
                foreach ($node->getSubNodeNames() as $property) {
                    $value = $node->$property;
                    if ($value instanceof Node) {
                        $value->setAttribute('parent', $node);
                    } elseif (is_array($value)) {
                        foreach ($value as $child) {
                            if ($child instanceof Node) {
                                $child->setAttribute('parent', $node);
                            }
                        }
                    }
                }

return null;
            }
        });
        $traverser->addVisitor($visitor);
        $traverser->traverse($nodes);

        return $visitor->structures;
    }
}
