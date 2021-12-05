<?php

namespace Hgabka\UtilsBundle\Export\Writer;

use Hgabka\UtilsBundle\Helper\SimpleXmlElementExtended;

class XmlWriter implements TypedWriterInterface
{
    /** @var SimpleXmlElementExtended */
    protected $xml;

    /** @var string */
    protected $rootTag;

    /** @var string */
    protected $rowTag;

    /** @var bool */
    protected $useCDATA;

    /** @var string */
    private $filename;

    public function __construct($filename, $rootTag, $rowTag, $useCDATA = true)
    {
        $this->filename = $filename;
        $this->rootTag = $rootTag;
        $this->useCDATA = $useCDATA;
        $this->rowTag = $rowTag;
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
        $this->xml = new SimpleXmlElementExtended('<?xml version=\"1.0\" encoding=\"utf-8\" ?><' . $this->rootTag . ' />');
    }

    public function write(array $data)
    {
        /** @var SimpleXmlElementExtended $product */
        $root = $this->xml->addChild($this->rowTag);
        foreach ($data as $header => $value) {
            if (!\is_array($value)) {
                if ($this->useCDATA) {
                    $root->addChildWithCDATA($header, $value);
                } else {
                    $root->addChild($header, htmlspecialchars($value, \ENT_XML1 | \ENT_COMPAT, 'UTF-8'));
                }
            } else {
                $this->addNodesFromArray($root, $value);
            }
        }
    }

    public function close()
    {
        $this->xml->asXML($this->filename);
    }

    protected function addNodesFromArray($parent, $data)
    {
        foreach ($data as $subKey => $subData) {
            if (\is_string($subData)) {
                if ($this->useCDATA) {
                    $parent->addChildWithCDATA($subKey, $subData);
                } else {
                    $parent->addChild($subKey, htmlspecialchars($subData, \ENT_XML1 | \ENT_COMPAT, 'UTF-8'));
                }
            } else {
                $node = $parent->addChild($subKey);
                $this->addNodesFromArray($node, $subData);
            }
        }
    }
}
