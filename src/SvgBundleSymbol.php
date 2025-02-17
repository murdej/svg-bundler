<?php declare(strict_types=1);

namespace Murdej\SvgBundler;

class SvgBundleSymbol
{
    public function __construct(
        public string $name,
        public string $srcPath,
    )
    {
    }
}