<?php

declare(strict_types = 1);

namespace Pamald\PamaldNpm;

use Pamald\Pamald\PackageCollectorInterface;

class PackageCollector implements PackageCollectorInterface
{

    /**
     * @var array<string, mixed>
     */
    protected array $lock;

    /**
     * @var array<string, mixed>
     */
    protected ?array $json;

    /**
     * @var array<string, \Pamald\Pamald\PackageInterface>
     */
    protected array $packages;

    /**
     * {@inheritdoc}
     */
    public function collect(?array $lock, ?array $json): array
    {
        if (!$lock) {
            return [];
        }

        $this->lock = $lock;
        $this->json = $json;
        $this->packages = [];
        // @todo Support for "lockfileVersion".
        // Version "3" uses "packages".
        // Version "1" uses "dependencies".
        foreach ($this->lock['packages'] ?? [] as $lockKey => $lockEntry) {
            if ($lockKey === '') {
                continue;
            }

            $name = $this->parsePackageName((string) $lockKey);
            $typeOfRelationship = $this->getTypeOfRelationship($name, $json);

            $package = new NormalPackage(
                $name,
                $lockEntry,
                $typeOfRelationship['type'] ?? null,
                $typeOfRelationship['versionConstraint'] ?? null,
                [],
            );

            $this->packages[$package->name()] = $package;
        }

        return $this->packages;
    }

    public function parsePackageName(string $lockKey): string
    {
        return preg_replace('@^node_modules/@', '', $lockKey);
    }

    /**
     * @param string $name
     * @param null|array<string, mixed> $json
     *
     * @phpstan-return null|pamald-npm-relationship
     */
    public function getTypeOfRelationship(string $name, ?array $json): ?array
    {
        if (!$json) {
            return null;
        }

        $dependencyTypes = [
            'dependencies',
            'optionalDependencies',
            'peerDependencies',
            'devDependencies',
        ];

        foreach ($dependencyTypes as $type) {
            if (!empty($json[$type][$name])) {
                return [
                    'type' => $type,
                    'versionConstraint' => $json[$type][$name],
                ];
            }
        }

        return null;
    }
}
