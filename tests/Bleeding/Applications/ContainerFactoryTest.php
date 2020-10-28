<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests\Bleeding\Applications;

use Bleeding\Applications\ContainerFactory;
use Psr\Container\ContainerInterface;
use Tests\TestCase;

use function putenv;
use function unlink;

/**
 * @package Tests\Bleeding\Applications
 * @coversDefaultClass \Bleeding\Applications\ContainerFactory
 * @immutable
 */
final class ContainerFactoryTest extends TestCase
{
    /**
     * @test
     * @covers ::create
     * @covers ::addDefinitions
     * @covers ::resolveDefinitionsPath
     */
    public function testCreate(): void
    {
        $container = ContainerFactory::create();

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }

    /**
     * @test
     * @covers ::create
     * @covers ::addDefinitions
     * @covers ::resolveDefinitionsPath
     */
    public function testCreateWithCache(): void
    {
        putenv('DEBUG_MODE=false');
        $cacheDir = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..', 'cache']);
        @unlink($cacheDir . '/CompiledContainer.php');

        $container = ContainerFactory::create($cacheDir);

        $this->assertInstanceOf(ContainerInterface::class, $container);
    }
}
