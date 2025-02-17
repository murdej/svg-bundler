<?php

use Murdej\SvgBundler\SvgBundler;

require_once __DIR__ . '/../vendor/autoload.php';

$bundler = new SvgBundler(
    __DIR__ . '/svgs',
    // __DIR__ . '/tmp/svgs.svg'
);

$bundle = $bundler->bundle();

echo $bundle->svgContent;

foreach ($bundle->symbols as $item) {
    echo '<svg width="32" height="32"><use xlink:href="#' . $item->name . '"></use></svg>' . $item->srcPath . '<br />'."\n";
}
