<?php
/**
 * PHPStan bootstrap stub for PHPUnit attribute metadata on CI images with PHPUnit 9.
 */
namespace PHPUnit\Framework\Attributes;

#[\Attribute(\Attribute::TARGET_METHOD)]
class DataProvider
{
    /**
     * @param string $methodName
     */
    public function __construct(
        public readonly string $methodName
    ) {
    }
}
