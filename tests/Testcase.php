<?php

namespace Rougin\Ezekiel;

use LegacyPHPUnit\TestCase as Legacy;

/**
 * @codeCoverageIgnore
 *
 * @package Ezekiel
 *
 * @author Rougin Gutib <rougingutib@gmail.com>
 */
class Testcase extends Legacy
{
    /**
     * @param string $needle
     * @param string $haystack
     *
     * @return void
     */
    public function doAssertContains($needle, $haystack)
    {
        /** @phpstan-ignore-next-line */
        if (method_exists($this, 'assertStringContainsString'))
        {
            /** @phpstan-ignore-next-line */
            $this->assertStringContainsString($needle, $haystack);

            return;
        }

        /** @phpstan-ignore-next-line */
        $this->assertContains($needle, $haystack);
    }

    /**
     * @param class-string $exception
     *
     * @return void
     */
    public function doExpectException($exception)
    {
        /** @phpstan-ignore-next-line */
        if (method_exists($this, 'expectException'))
        {
            /** @phpstan-ignore-next-line */
            $this->expectException($exception);

            return;
        }

        /** @phpstan-ignore-next-line */
        $this->setExpectedException($exception);
    }
}
