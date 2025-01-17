<?php

namespace Hgabka\UtilsBundle\Twig\Extension;

use Hgabka\UtilsBundle\Asset\TagRenderer;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AdvancedEntryFilesTwigExtension extends AbstractExtension
{
    protected $tagRenderer;

    public function __construct(TagRenderer $tagRenderer)
    {
        $this->tagRenderer = $tagRenderer;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('encore_entry_link_tags_advanced', $this->renderWebpackLinkTags(...), ['is_safe' => ['html']]),
        ];
    }

    public function renderWebpackLinkTags(string $entryName, ?string $packageName = null, string $entrypointName = '_default'): string
    {
        return $this->tagRenderer
            ->renderWebpackLinkTags($entryName, $packageName, $entrypointName);
    }
}
