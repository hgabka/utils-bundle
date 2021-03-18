<?php

namespace Hgabka\UtilsBundle\Export\Writer;

interface WriterInterface
{
    public function open();

    public function write(array $data);

    public function close();
}
