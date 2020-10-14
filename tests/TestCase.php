<?php

/**
 * @author Masaru Yamagishi <m-yamagishi@infiniteloop.co.jp>
 * @copyright 2020- Masaru Yamagishi
 */

declare(strict_types=1);

namespace Tests;

use Bleeding\ContainerFactory;
use DI\Container;
use PHPUnit\Framework\TestCase as TestCaseBase;

/**
 * TestCase
 * @package Tests
 */
abstract class TestCase extends TestCaseBase
{
    /**
     * Create Bleeding container
     *
     * @return Container
     */
    protected function createContainer(): Container
    {
        return ContainerFactory::create();
    }
}
