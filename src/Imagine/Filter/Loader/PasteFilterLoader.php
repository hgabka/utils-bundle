<?php

namespace Hgabka\UtilsBundle\Imagine\Filter\Loader;

use Hgabka\UtilsBundle\Imagine\Filter\Paste;
use Imagine\Image\ImageInterface;
use Imagine\Image\ImagineInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class PasteFilterLoader implements LoaderInterface
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
        [$width, $height] = $options['size'];

        $filter = new Paste(
            $this->imagine,
            $width,
            $height,
            $options['position'] ?? 'center',
            $options['color'] ?? '#fff',
            $options['transparency'] ?? 0
        );

        return $filter->apply($image);
    }
}
