# Custom rules

```php
final class NoLegacyRule implements ArchitectureGuard\Contracts\ArchitectureRule {
    public function identifier(): string { return 'no_legacy'; }
    public function description(): string { return 'Reject legacy references.'; }
    public function analyze(ArchitectureGuard\Core\RuleContext $context): array { return []; }
}
```

Register an instance with `RuleRegistry::register()` in the consuming application's provider and add `'no_legacy' => true` to `architecture-guard.rules`. Rules receive parsed structures and a graph; they should not reread source files or parse AST again.
