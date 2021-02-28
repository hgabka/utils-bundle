<?php

namespace Hgabka\UtilsBundle\Export\Writer;

use Hgabka\UtilsBundle\Helper\SimpleXmlElementExtended;

class XmlWriter implements TypedWriterInterface
{
    /** @var SimpleXmlElementExtended */
    protected $xml;

    /** @var string */
    private $filename;

    public function __construct($filename)
    {
        $this->filename = $filename;
    }

    /**
     * {@inheritdoc}
     */
    final public function getDefaultMimeType()
    {
        return 'application/xml';
    }

    /**
     * {@inheritdoc}
     */
    final public function getFormat()
    {
        return 'xml';
    }

    public function open()
    {
        $this->xml = new SimpleXmlElementExtended('<?xml version=\"1.0\" encoding=\"utf-8\" ?><products />');
    }

    public function write(array $data)
    {
        /** @var SimpleXmlElementExtended $product */
        $product = $this->xml->addChild('product');
        foreach ($data as $header => $value) {
            $product->addChildWithCDATA($header, $value);
        }
    }

    public function close()
    {
        $this->xml->asXML($this->filename);
    }
}
