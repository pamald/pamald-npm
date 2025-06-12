<?php

declare(strict_types = 1);

namespace Pamald\PamaldNpm\Tests\Unit;

use Pamald\Pamald\LockDiffer;
use Pamald\Pamald\Reporter\ConsoleTableReporter;
use Pamald\PamaldNpm\PackageDependency;
use Pamald\PamaldNpm\DependencyCollector;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;

/**
 * @phpstan-import-type PamaldConsoleTableReporterOptions from \Pamald\Pamald\Phpstan
 */
#[CoversClass(DependencyCollector::class)]
#[CoversClass(PackageDependency::class)]
class ConsoleTableReporterTest extends TestBase
{

    /**
     * @var resource[]
     */
    protected array $streams = [];

    protected function tearDown(): void
    {
        parent::tearDown();

        foreach ($this->streams as $stream) {
            fclose($stream);
        }
        $this->streams = [];
    }

    /**
     * @return array<string, mixed[]>
     */
    public static function casesGenerate(): array
    {
        $projectDir = static::fixturesDir('project-01');

        return [
            'basic' => [
                // phpcs:disable Generic.Files.LineLength.TooLong
                'expected' => <<< 'TEXT'
                    +---------------+-----------+-----------+---------+---------+----------+----------+------------+------------+---------+---------+
                    | Name          | L Version | R Version | L Type  | R Type  | L Link   | R Link   | L Env      | R Env      | L Depth | R Depth |
                    +---------------+-----------+-----------+---------+---------+----------+----------+------------+------------+---------+---------+
                    | Production - Direct                                                                                                           |
                    | find-versions | 5.0.0     | 5.1.0     | package | package | required | required | production | production | direct  | direct  |
                    | Other                                                                                                                         |
                    | semver-regex  | 4.0.5     | 4.2.0     | package | package |          |          |            |            | child   | child   |
                    +---------------+-----------+-----------+---------+---------+----------+----------+------------+------------+---------+---------+

                    TEXT,
                // phpcs:enable Generic.Files.LineLength.TooLong
                'leftLock' => json_decode(file_get_contents("$projectDir/01-lock.json") ?: '{}', true),
                'leftJson' => json_decode(file_get_contents("$projectDir/01.json") ?: '{}', true),
                'rightLock' => json_decode(file_get_contents("$projectDir/02-lock.json") ?: '{}', true),
                'rightJson' => json_decode(file_get_contents("$projectDir/02.json") ?: '{}', true),
                'options' => [],
            ],
        ];
    }

    /**
     * @param null|array<string, mixed> $leftLock
     * @param null|array<string, mixed> $leftJson
     * @param null|array<string, mixed> $rightLock
     * @param null|array<string, mixed> $rightJson
     * @phpstan-param PamaldConsoleTableReporterOptions $options
     */
    #[Test]
    #[DataProvider('casesGenerate')]
    public function testGenerate(
        string $expected,
        ?array $leftLock = null,
        ?array $leftJson = null,
        ?array $rightLock = null,
        ?array $rightJson = null,
        array $options = [],
    ): void {
        if (!isset($options['stream'])) {
            $options['stream'] = static::createStream();
        }
        $this->streams[] = $options['stream'];

        $packageCollector = new DependencyCollector();
        $differ = new LockDiffer();
        $entries = $differ->diff(
            $packageCollector->collect($leftLock, $leftJson),
            $packageCollector->collect($rightLock, $rightJson),
        );
        (new ConsoleTableReporter())
            ->setOptions($options)
            ->generate($entries);
        rewind($options['stream']);
        static::assertSame(
            $expected,
            stream_get_contents($options['stream']),
        );
    }

    /**
     * @return resource
     */
    protected static function createStream()
    {
        $filePath = 'php://memory';
        $resource = fopen($filePath, 'rw');
        if ($resource === false) {
            throw new \RuntimeException("file $filePath could not be opened");
        }

        return $resource;
    }
}
