<?php

declare(strict_types = 1);

namespace Pamald\PamaldNpm;

use Pamald\Pamald\DependencyCollectorInterface;
use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;

/**
 * @phpstan-import-type PamaldNpmRelationship from \Pamald\PamaldNpm\Phpstan
 */
class DependencyCollector implements DependencyCollectorInterface
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
     * @var array<string, \Pamald\Pamald\DependencyInterface>
     */
    protected array $dependencies;

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
        $this->dependencies = [];
        // @todo Support for "lockfileVersion".
        // Version "3" uses "packages".
        // Version "1" uses "dependencies".
        foreach ($this->lock['packages'] ?? [] as $lockKey => $lockEntry) {
            if ($lockKey === '') {
                continue;
            }

            $name = $this->parsePackageName((string) $lockKey);
            $typeOfRelationship = $this->getTypeOfRelationship($name, $json);

            $package = new PackageDependency(
                $name,
                $lockEntry,
                $typeOfRelationship['type'],
                $typeOfRelationship['link'],
                $typeOfRelationship['environment'],
                $typeOfRelationship['versionConstraint'],
                [],
            );

            $this->dependencies[$package->name()] = $package;
        }

        return $this->dependencies;
    }

    public function parsePackageName(string $lockKey): string
    {
        return preg_replace('@^node_modules/@', '', $lockKey);
    }

    /**
     * @param string $name
     * @param null|array<string, mixed> $json
     *
     * @phpstan-return null|PamaldNpmRelationship
     */
    public function getTypeOfRelationship(string $name, ?array $json): ?array
    {
        if (isset($json['dependencies'][$name])) {
            return [
                'type' => DependencyType::Package,
                'link' => DependencyLink::Required,
                'environment' => DependencyEnvironment::Production,
                'versionConstraint' => $json['dependencies'][$name],
            ];
        }

        if (isset($json['optionalDependencies'][$name])) {
            return [
                'type' => DependencyType::Package,
                'link' => DependencyLink::Optional,
                'environment' => DependencyEnvironment::Production,
                'versionConstraint' => $json['optionalDependencies'][$name],
            ];
        }

        if (isset($json['peerDependencies'][$name])) {
            return [
                'type' => DependencyType::Peer,
                'link' => DependencyLink::Required,
                'environment' => DependencyEnvironment::Production,
                'versionConstraint' => $json['peerDependencies'][$name],
            ];
        }

        if (isset($json['devDependencies'][$name])) {
            return [
                'type' => DependencyType::Package,
                'link' => DependencyLink::Required,
                'environment' => DependencyEnvironment::Development,
                'versionConstraint' => $json['devDependencies'][$name],
            ];
        }

        return [
            'type' => DependencyType::Package,
            'link' => null,
            'environment' => null,
            'versionConstraint' => null,
        ];
    }
}
