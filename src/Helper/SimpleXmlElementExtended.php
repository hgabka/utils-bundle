<?php

namespace App\Helper;

use SimpleXMLElement;

class SimpleXmlElementExtended extends SimpleXMLElement
{
    /**
     * Adds a child with $value inside CDATA.
     *
     * @param string $name
     * @param mixed  $value
     *
     * @return SimpleXMLElement
     */
    public function addChildWithCDATA($name, $value = null)
    {
        $new_child = $this->addChild($name);

        if (null !== $new_child) {
            $node = dom_import_simplexml($new_child);
            $no = $node->ownerDocument;
            $node->appendChild($no->createCDATASection($value));
        }

        return $new_child;
    }
}
