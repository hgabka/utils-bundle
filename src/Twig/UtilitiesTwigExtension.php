<?php

namespace Hgabka\UtilsBundle\Twig;

use Hgabka\UtilsBundle\Helper\SlugifierInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class UtilitiesTwigExtension extends AbstractExtension
{
    /**
     * @var SlugifierInterface
     */
    private $slugifier;

    /**
     * @param $slugifier
     */
    public function __construct($slugifier)
    {
        $this->slugifier = $slugifier;
    }

    /**
     * Returns a list of filters.
     *
     * @return array An array of filters
     */
    public function getFilters(): array
    {
        return [
            new TwigFilter('slugify', [$this, 'slugify']),
        ];
    }

    /**
     * @param string $text
     *
     * @return string
     */
    public function slugify($text)
    {
        return $this->slugifier->slugify($text, '');
    }
}
