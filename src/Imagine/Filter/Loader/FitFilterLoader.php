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
     *
     * @param ImagineInterface $imagine
     */
    public function __construct(ImagineInterface $imagine)
    {
        $this->imagine = $imagine;
    }

    /**
     * @param ImageInterface $image
     * @param array          $options
     *
     * @return mixed
     */
    public function load(ImageInterface $image, array $options = [])
    {
        list($width, $height) = $options['size'];

        $fitFilter = new Fit($width, $height);

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
