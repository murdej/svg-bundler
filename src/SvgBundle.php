<?php declare(strict_types=1);

namespace Murdej\SvgBundler;

class SvgBundle
{
    /**
     * @var SvgBundleSymbol[]
     */
    public array $symbols = [];

    public ?string $svgContent = null;
}