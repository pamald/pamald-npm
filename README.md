# pamald NPM

[![CircleCI](https://circleci.com/gh/pamald/pamald-npm/tree/1.x.svg?style=svg)](https://circleci.com/gh/pamald/pamald-npm/?branch=1.x)
[![codecov](https://codecov.io/gh/pamald/pamald-npm/branch/1.x/graph/badge.svg?token=HSF16OGPyr)](https://app.codecov.io/gh/pamald/pamald-npm/branch/1.x)


**pamald-npm** is a PHP library that collects dependency information from `package-lock.json` files.
It is the NPM-specific implementation of the [pamald/pamald] project,
enabling detailed analysis of NPM packages.


## Project Goals

The library aims to collect and analyze NPM project dependencies from
`package-lock.json` and `package.json` files in a structured way.
This is particularly useful for:

- **Tracking dependency changes**: Easily identify package updates, new dependencies, or removed packages
- **Security audits**: Quick overview of package versions in use
- **CI/CD pipelines**: Automated dependency analysis during build processes
- **Documentation**: Generate accurate dependency lists for projects
- **Prepare Git Commit Message**: Automatically generate a commit message with dependency information


## Why is it Useful?

### 1. Comprehensive Dependency Information

The library collects not only packages, but also:
- **NodeJS version** requirements
- **package** requirements
- **Distinguishes between direct and transitive dependencies**
- **Groups by production and development environments**


### 3. Integration with the pamald Ecosystem

The library implements the [pamald/pamald] base library interfaces,
making it easily integrable with other pamald tools (composer, yarn) and Robo tasks.


## Usage

```php
<?php

declare(strict_types = 1);

use Pamald\PamaldNpm\DependencyCollector;

$collector = new DependencyCollector();

$lock = json_decode(file_get_contents('package-lock.json'), true);
$json = json_decode(file_get_contents('package.json'), true);

$rightDependencies = $collector->collect($lock, $json);
// See pamald/pamald how to use this to generate a report.
```


## Links

- [pamald/pamald]
- [pamald/pamald-composer]
- [pamald/pamald-npm]
- [pamald/pamald-yarn]
- [pamald/robo-pamald]
- [pamald/robo-pamald-composer]
- [pamald/robo-pamald-npm]
- [pamald/robo-pamald-yarn]


[pamald/pamald]: https://github.com/pamald/pamald
[pamald/pamald-composer]: https://github.com/pamald/pamald-composer
[pamald/pamald-npm]: https://github.com/pamald/pamald-npm
[pamald/pamald-yarn]: https://github.com/pamald/pamald-yarn
[pamald/robo-pamald]: https://github.com/pamald/robo-pamald
[pamald/robo-pamald-composer]: https://github.com/pamald/robo-pamald-composer
[pamald/robo-pamald-npm]: https://github.com/pamald/robo-pamald-npm
[pamald/robo-pamald-yarn]: https://github.com/pamald/robo-pamald-yarn
