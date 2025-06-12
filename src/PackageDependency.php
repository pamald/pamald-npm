<?php

declare(strict_types = 1);

namespace Pamald\PamaldNpm;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyInterface;
use Pamald\Pamald\DependencyJsonSerializerTrait;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Sweetchuck\Utils\VersionNumber;

class PackageDependency implements DependencyInterface
{
    use DependencyJsonSerializerTrait;

    protected ?VersionNumber $version = null;

    /**
     * @param array<string, mixed> $lockEntry
     * @param array<string, mixed> $patches
     */
    public function __construct(
        protected string $name,
        protected array $lockEntry,
        protected ?DependencyType $type,
        protected ?DependencyLink $link,
        protected ?DependencyEnvironment $environment,
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

    public function type(): ?DependencyType
    {
        return $this->type;
    }

    public function link(): ?DependencyLink
    {
        return $this->link;
    }

    public function environment(): ?DependencyEnvironment
    {
        return $this->environment;
    }

    public function versionString(): ?string
    {
        return $this->lockEntry['version'];
    }

    public function version(): ?VersionNumber
    {
        return $this->version;
    }

    public function isDirectDependency(): ?bool
    {
        return $this->environment !== null;
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
