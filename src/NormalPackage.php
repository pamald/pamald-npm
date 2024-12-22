<?php

declare(strict_types = 1);

namespace Pamald\PamaldNpm;

use Pamald\Pamald\PackageInterface;
use Pamald\Pamald\PackageJsonSerializerTrait;
use Sweetchuck\Utils\VersionNumber;

class NormalPackage implements PackageInterface
{
    use PackageJsonSerializerTrait;

    protected ?VersionNumber $version = null;

    /**
     * @param array<string, mixed> $lockEntry
     * @param array<string, mixed> $patches
     */
    public function __construct(
        protected string $name,
        protected array $lockEntry,
        protected ?string $typeOfRelationship = null,
        protected ?string $versionConstraint = null,
        protected array $patches = [],
    ) {
        $versionString = $this->lockEntry['version'];
        if (!empty($versionString) && VersionNumber::isValid($versionString)) {
            $this->version = VersionNumber::createFromString($versionString);
        }
    }

    public function name(): string
    {
        return $this->name;
    }

    public function type(): ?string
    {
        return null;
    }

    public function versionString(): ?string
    {
        return $this->lockEntry['version'];
    }

    public function version(): ?VersionNumber
    {
        return $this->version;
    }

    public function typeOfRelationship(): ?string
    {
        return $this->typeOfRelationship;
    }

    public function isDirectDependency(): ?bool
    {
        return $this->typeOfRelationship !== null;
    }

    public function homepage(): ?string
    {
        return null;
    }

    public function vcsInfo(): ?array
    {
        return null;
    }

    public function issueTracker(): ?array
    {
        return null;
    }
}
