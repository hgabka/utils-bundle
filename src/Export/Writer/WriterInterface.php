<?php

namespace Hgabka\UtilsBundle\Export\Writer;

interface WriterInterface
{
    public function open();

    /**
     * @param array $data
     */
    public function write(array $data);

    public function close();
}
