<?php

namespace Hgabka\UtilsBundle\Helper;

use SimpleXMLElement;

class SimpleXmlElementExtended extends SimpleXMLElement
{
    /**
     * Adds a child with $value inside CDATA.
     *
     * @param string $name
     * @param mixed  $value
     * @param null   $namespace
     *
     * @return SimpleXmlElementExtended
     */
    public function addChildWithCDATA(string $name, $value = null, $namespace = null): self
    {
        $newChild = $this->addChild($name, null, $namespace);

        if (null !== $newChild) {
            $node = dom_import_simplexml($newChild);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection($value));
        }

        return $newChild;
    }
}
