<?php

namespace Hgabka\UtilsBundle\Imagine\Filter\Loader;

use Hgabka\UtilsBundle\Imagine\Filter\Fit;
use Hgabka\UtilsBundle\Imagine\Filter\Paste;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class FitFilterLoader implements LoaderInterface
{
    /** @var ImagineInterface */
    protected $imagine;

    /**
     * FitFilterLoader constructor.
     */
    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * @return mixed
     */
    public function load(ImageInterface $image, array $options = [])
    {
        [$width, $height] = $options['size'];
        [$insideWidth, $insideHeight] = $options['inside_size'] ?? [null, null];
        if (0 === (int)$insideWidth || $insideWidth >= $width) {
            $insideWidth = null;
        }
        if (0 === (int)$insideHeight || $insideHeight >= $height) {
            $insideHeight = null;
        }
        if (null === $insideWidth || null === $insideHeight) {
            $insideWidth = $insideHeight = null;
        }
        $mode = !isset($options['mode']) ? Fit::MODE_INSET : $options['mode'];

        $fitFilter = new Fit($insideWidth ?? $width, $insideHeight ?? $height, $mode);

        $image = $fitFilter->apply($image);

        $pasteFilter = new Paste(
            $this->imagine,
            $width,
            $height,
            $options['position'] ?? 'center',
            $options['background_color'] ?? '#fff',
            $options['transparency'] ?? 0
        );

        return $pasteFilter->apply($image);
    }
}
