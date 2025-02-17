<?php declare(strict_types=1);

namespace Murdej\SvgBundler;

use DOMDocument;

class SvgBundler
{
    /**
     * @param string|string[] $sources
     * @param ?string $target
     * @param callable|null $nameTransformer
     */
    public function __construct(
        public string|array $sources,
        public ?string $target = null,
        public ?\callable $nameTransformer = null,
        public array $skipSvgAttributes = ['width', 'height'],
    )
    {
    }

    /**
     * @return SvgBundleSymbol[]
     */
    public function bundle(): SvgBundle
    {
        $dom = new DOMDocument('1.0', 'UTF-8');

        $svg = $dom->createElement('svg');
        $svg->setAttribute('xmlns', 'http://www.w3.org/2000/svg');
        $svg->setAttribute('width', '0');
        $svg->setAttribute('height', '0');
        $svg->setAttribute('viewBox', '0 0 0 0');
        $dom->appendChild($svg);
        $defs = $dom->createElement('defs');
        $svg->appendChild($defs);

        $res = new SvgBundle();

        $files = is_string($this->sources)
            ? array_map(fn(string $fn) => rtrim($this->sources) . '/' . $fn, scandir($this->sources))
            : $this->sources;

        foreach ($files as $key => $file) {
            $name = pathinfo($file, PATHINFO_FILENAME);
            if ($name === '.' || $name === '..' || $name === '') continue;
            $symbolId = $this->nameTransformer
                ? ($this->nameTransformer)($file)
                : (is_string($key)
                    ? $key
                    : $name
                );

            $res->symbols[] = new SvgBundleSymbol($symbolId, $file);
            $symbol = $dom->createElement('symbol');
            $defs->appendChild($symbol);
            $this->svgToSymbol($file, $symbol, $symbolId);
        }

        // $dom = dom_import_simplexml($svg)->ownerDocument;
        $dom->formatOutput = true;
        if ($this->target)
            $dom->save($this->target);
        else
            $res->svgContent = $dom->saveXML();

        return $res;
    }

    public function svgToSymbol(string $svgPath, \DOMElement $symbol, string $symbolId): void {
        $svgDom = new DOMDocument();
        $svgDom->load($svgPath);

        $svg = $svgDom->documentElement;

        foreach ($svg->childNodes as $child) {
            $symbol->appendChild($symbol->ownerDocument->importNode($child, true));
        }
        foreach ($svg->attributes as $attribute) {
            if (in_array($attribute->name, $this->skipSvgAttributes)) continue;
            $symbol->setAttribute($attribute->name, $attribute->value);
        }
        $symbol->setAttribute('id', $symbolId);
    }
}