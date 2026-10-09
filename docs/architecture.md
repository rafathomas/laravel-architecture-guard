# Architecture

`PhpFileScanner` yields readable PHP files once, excluding configured path segments and symbolic links. `AstDependencyExtractor` parses each file once with nikic/php-parser, uses its name resolver, and emits `SourceStructure` records containing effective references and source lines. Imports by themselves do not become dependencies.

`ArchitectureEngine` builds a shared `DependencyGraph` and passes a `RuleContext` to registered rules. The graph stores forward and reverse adjacency lists; direct lookup is O(1) plus output size. Cycle detection is depth-first and O(V + E); equivalent rotations are deduplicated. The current implementation intentionally reports only cycles entirely represented in scanned source.

Laravel wiring only assembles dependencies and registers commands. No project source is inspected during provider boot.
