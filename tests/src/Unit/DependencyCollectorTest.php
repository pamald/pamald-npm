<?php

declare(strict_types = 1);

namespace Pamald\PamaldNpm\Tests\Unit;

use Pamald\Pamald\DependencyEnvironment;
use Pamald\Pamald\DependencyLink;
use Pamald\Pamald\DependencyType;
use Pamald\PamaldNpm\DependencyCollector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

#[CoversClass(DependencyCollector::class)]
class DependencyCollectorTest extends TestBase
{

    /**
     * @return array<string, mixed>
     */
    public static function casesParseLockKey(): array
    {
        return [
            'basic 1' => [
                'expected' => 'foo',
                'lockKey' => 'node_modules/foo',
            ],
        ];
    }

    #[Test]
    #[DataProvider('casesParseLockKey')]
    public function testParseLockKey(string $expected, string $lockKey): void
    {
        $collector = new DependencyCollector();
        static::assertSame($expected, $collector->parsePackageName($lockKey));
    }

    /**
     * @return array<string, mixed>
     */
    public static function casesCollect(): array
    {
        $projectDir = static::fixturesDir('project-01');

        return [
            'empty' => [
                'expected' => [],
                'lock' => [],
                'json' => null,
            ],
            'basic' => [
                'expected' => [
                    'find-versions' => [
                        'name' => 'find-versions',
                        'type' => DependencyType::Package,
                        'link' => DependencyLink::Required,
                        'environment' => DependencyEnvironment::Production,
                        'versionString' => '5.0.0',
                        'isDirectDependency' => true,
                    ],
                    'semver-regex' => [
                        'name' => 'semver-regex',
                        'type' => DependencyType::Package,
                        'link' => null,
                        'environment' => null,
                        'versionString' => '4.0.5',
                        'isDirectDependency' => false,
                    ],
                ],
                'lock' => json_decode(file_get_contents("$projectDir/01-lock.json") ?: '{}', true),
                'json' => json_decode(file_get_contents("$projectDir/01.json") ?: '{}', true),
            ],
        ];
    }

    /**
     * @param array<string, mixed> $expected
     * @param array<string, mixed> $lock
     * @param null|array<string, mixed> $json
     */
    #[Test]
    #[DataProvider('casesCollect')]
    public function testCollect(array $expected, array $lock, ?array $json): void
    {
        $collector = new DependencyCollector();
        $actual = $collector->collect($lock, $json);
        static::assertSame(array_keys($expected), array_keys($actual));
        foreach ($expected as $id => $expectedValues) {
            if (array_key_exists('name', $expectedValues)) {
                static::assertSame(
                    $expectedValues['name'],
                    $actual[$id]->name(),
                    "$id::name",
                );
            }

            if (array_key_exists('type', $expectedValues)) {
                static::assertEquals(
                    $expectedValues['type'],
                    $actual[$id]->type(),
                    "$id::type",
                );
            }

            if (array_key_exists('link', $expectedValues)) {
                static::assertEquals(
                    $expectedValues['link'],
                    $actual[$id]->link(),
                    "$id::link",
                );
            }

            if (array_key_exists('environment', $expectedValues)) {
                static::assertSame(
                    $expectedValues['environment'],
                    $actual[$id]->environment(),
                    "$id::environment",
                );
            }

            if (array_key_exists('versionString', $expectedValues)) {
                static::assertSame(
                    $expectedValues['versionString'],
                    $actual[$id]->versionString(),
                    "$id::versionString",
                );
            }

            if (array_key_exists('isDirectDependency', $expectedValues)) {
                static::assertSame(
                    $expectedValues['isDirectDependency'],
                    $actual[$id]->isDirectDependency(),
                    "$id::isDirectDependency",
                );
            }
        }
    }
}
