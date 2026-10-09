# Configuration

`paths` accepts one or more roots. `exclude` is a list of directory names. Enable only rules needed by the project through `rules`. Prefixes are namespace-boundary aware: `App\Foo\` does not match `App\Foobar\`.

For `namespace_conventions`, map a relative directory name to an expected namespace. For controller responsibilities, configure controller and forbidden namespace prefixes explicitly; no length or complexity heuristic is applied.
