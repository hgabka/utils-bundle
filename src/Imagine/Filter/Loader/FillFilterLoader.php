<?php

namespace Hgabka\UtilsBundle\Imagine\Filter\Loader;

use Hgabka\UtilsBundle\Imagine\Filter\Fill;
use Imagine\Image\ImageInterface;
use Liip\ImagineBundle\Imagine\Filter\Loader\LoaderInterface;

class FillFilterLoader implements LoaderInterface
{
    /**
     * @param ImageInterface $image
     * @param array          $options
     *
     * @return ImageInterface
     */
    public function load(ImageInterface $image, array $options = [])
    {
        [$width, $height] = $options['size'];

        $filter = new Fill($width, $height, $options['position'] ?? 'center');

        return $filter->apply($image);
    }
}
