<?php

declare(strict_types = 1);

namespace Pamald\PamaldNpm\Tests\Unit;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Pamald\PamaldNpm\PackageDependency;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(PackageDependency::class)]
class PackageDependencyTest extends TestBase
{
    #[Test]
    public function testGetters(): void
    {
        $name = 'my-pack1';
        $lockEntry = [
            'version' => '1.2.3',
        ];
        $dependency = new PackageDependency(
            $name,
            $lockEntry,
            DependencyType::Package,
            DependencyLink::Required,
            DependencyEnvironment::Production,
            '^1.2',
        );

        static::assertSame('my-pack1', $dependency->name());
        static::assertEquals(DependencyType::Package, $dependency->type());
        static::assertEquals(DependencyLink::Required, $dependency->link());
        static::assertEquals(DependencyEnvironment::Production, $dependency->environment());
        static::assertSame('1.2.3', $dependency->versionString());
        static::assertSame(true, $dependency->isDirectDependency());
        static::assertSame(null, $dependency->issueTracker());
        static::assertSame('1.2.3', (string) $dependency->version());
        static::assertSame(null, $dependency->homepage());
        static::assertSame(null, $dependency->vcsInfo());
    }
}
