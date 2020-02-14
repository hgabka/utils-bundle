/**
 * Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

// This file contains style definitions that can be used by CKEditor plugins.
//
// The most common use for it is the "stylescombo" plugin, which shows a combo
// in the editor toolbar, containing all styles. Other plugins instead, like
// the div plugin, use a subset of the styles on their feature.
//
// If you don't have plugins that depend on this file, you can simply ignore it.
// Otherwise it is strongly recommended to customize this file to match your
// website requirements and design properly.

CKEDITOR.stylesSet.add( 'default', [
    /* Block Styles */

    // These styles are already available in the "Format" combo ("format" plugin),
    // so they are not needed here by default. You may enable them to avoid
    // placing the "Format" combo in the toolbar, maintaining the same features.
    /*
    { name: 'Paragraph',		element: 'p' },
    { name: 'Heading 1',		element: 'h1' },
    { name: 'Heading 2',		element: 'h2' },
    { name: 'Heading 3',		element: 'h3' },
    { name: 'Heading 4',		element: 'h4' },
    { name: 'Heading 5',		element: 'h5' },
    { name: 'Heading 6',		element: 'h6' },
    { name: 'Preformatted Text',element: 'pre' },
    { name: 'Address',			element: 'address' },
    */

    { name: 'Dőlt betűs cím',		element: 'h2', styles: { 'font-style': 'italic' } },
    { name: 'Dőlt szűrke cím',      element: 'h3', styles: { 'color': '#aaa', 'font-style': 'italic' } },
    { name: 'Fejléc 5 kisebb',      element: 'h5', styles: { 'font-size': '22px', 'line-height':'26px' } },
    { name: 'Fejléc 5 kisebb félvastag',      element: 'h5', styles: { 'font-size': '22px', 'font-weight':'500', 'line-height':'26px' } },
    { name: 'Félvastag2',			element: 'span', styles: { 'font-weight':'500' } },
    {
        name: 'Egyedi konténer',
        element: 'div',
        styles: {
            padding: '5px 10px',
            background: '#eee',
            border: '1px solid #ccc'
        }
    },

    /* Inline Styles */

    // These are core styles available as toolbar buttons. You may opt enabling
    // some of them in the Styles combo, removing them from the toolbar.
    // (This requires the "stylescombo" plugin)
    /*
    { name: 'Strong',			element: 'strong', overrides: 'b' },
    { name: 'Emphasis',			element: 'em'	, overrides: 'i' },
    { name: 'Underline',		element: 'u' },
    { name: 'Strikethrough',	element: 'strike' },
    { name: 'Subscript',		element: 'sub' },
    { name: 'Superscript',		element: 'sup' },
    */

    { name: 'Kék Címsor', element: 'h3', styles: { 'color': 'Blue' } },
    { name: 'Piros Címsor', element: 'h3', styles: { 'color': 'Red' } },
    { name: 'Marker',			element: 'span', attributes: { 'class': 'marker' } },

    { name: 'Keyboard Phrase',	element: 'kbd' },
    { name: 'Sample Text',		element: 'samp' },
    { name: 'Variable',			element: 'var' },

    { name: 'Deleted Text',		element: 'del' },
    { name: 'Inserted Text',	element: 'ins' },

    { name: 'Cited Work',		element: 'cite' },
    { name: 'Inline Quotation',	element: 'q' },

    { name: 'Language: RTL',	element: 'span', attributes: { 'dir': 'rtl' } },
    { name: 'Language: LTR',	element: 'span', attributes: { 'dir': 'ltr' } },

    { name: 'Sárga háttér', element: 'span', styles: { 'background-color': 'Yellow' } },
    { name: 'Zöld háttér', element: 'span', styles: { 'background-color': 'Lime' } },
    { name: 'Nagy', element: 'big' },
    { name: 'Kicsi', element: 'small' },
    { name: 'Írógép', element: 'tt' },
    { name: 'Komputer Kód', element: 'code' },
    { name: 'Törölt szöveg', element: 'del' },
    { name: 'Beszúrt szöveg', element: 'ins' },
    /* Object Styles */

    {
        name: 'Kép balra',
        element: 'img',
        attributes: { 'class': 'left' }
    },

    {
        name: 'Kép jobbra',
        element: 'img',
        attributes: { 'class': 'right' }
    },

    {
        name: 'Kép margó nélkül',
        element: 'img',
        styles: { 'margin-top':'0', 'margin-bottom':'0' }
    },

    {
        name: 'Kicsi táblázat',
        element: 'table',
        attributes: {
            cellpadding: '5',
            cellspacing: '0',
            border: '1',
            bordercolor: '#ccc'
        },
        styles: {
            'border-collapse': 'collapse'
        }
    },

    { name: 'Táblázat keret nélkül',		element: 'table',	styles: { 'border-style': 'hidden', 'background-color': '#E6E6FA' } },
    { name: 'Négyzetes felsorolás',  element: 'ul',    styles: { 'list-style-type': 'square' } },
    { name: 'Jelöletlen felsorolás',	element: 'ul',		styles: { 'list-style-type': 'none' } },
    {
        name: 'Lenyíló szöveg',
        element: 'p',
        styles: { 'margin-top':'0', 'line-height':'20px', 'margin-bottom':'0' }
    },
    {
        name: 'Általános szöveg',
        element: 'p',
        attributes: {
            'class':'general-os'
        }
    },
    {
        name: 'Általános szöveg margó nélkül',
        element: 'p',
        attributes: {
            'class':'general-os-no-margin'
        }
    },
    {
        name: 'Szöveg margó nélkül',
        element: 'p',
        attributes: {
            'style':'margin-top:0;line-height:20px'
        }
    },
    {
        name: 'Open Sans Light',
        element: 'p',
        attributes: {
            'style':'font-family: Open Sans,sans-serif;font-size: 12px;font-weight: 100;letter-spacing: -0.01em;line-height: 20px;'
        }
    },
    {
        name: 'Általános alcím',
        element: 'p',
        attributes: {
            'class':'page-title'
        }
    },
    {
        name: 'Alcím',
        element: 'p',
        attributes: {
            'class':'alcim'
        }
    },
    {
        name: 'Alcím 2',
        element: 'p',
        attributes: {
            'class':'alcim2'
        }
    },
    {
        name: 'Alcím 3',
        element: 'p',
        attributes: {
            'class':'alcim3'
        }
    },
    {
        name: 'Alcím 4',
        element: 'p',
        attributes: {
            'class':'page-subtitle'
        }
    },
    {
        name: 'Alcím 5',
        element: 'p',
        attributes: {
            'class':'page-subtitle2'
        }
    },
    {
        name: 'Alcím 6',
        element: 'p',
        attributes: {
            'class':'page-osw'
        }
    },
    {
        name: 'Alcím 7',
        element: 'p',
        attributes: {
            'class':'page-oswlight'
        }
    },
    {
        name: 'Félvastag',
        element: 'p',
        attributes: {
            'class':'page-title',
            'style':'font-weight:500'
        }
    },
    {
        name: 'Kézírás',
        element: 'p',
        attributes: {
            'class':'handwrite'
        }
    },
    {
        name: 'Fejléc 5 normál',
        element: 'h5',
        attributes: {
            'class':'alcim',
            'style': 'font-size:22px'
        }
    },
    {
        name: 'Fejléc 5 normál',
        element: 'h5',
        attributes: {
            'class':'alcim',
            'style': 'font-size:22px;font-weight:300'
        }
    },
    {
        name: 'Fejléc 5 kicsi',
        element: 'h5',
        attributes: {
            'class':'small'
        }
    },
    {
        name: 'Kék doboz',
        element: 'p',
        attributes: {
            'class':'kekdoboz'
        }
    },
    {
        name: 'Sárga doboz',
        element: 'p',
        attributes: {
            'class':'sargadoboz'
        }
    },
    {
        name: 'Zöld doboz',
        element: 'p',
        attributes: {
            'class':'zolddoboz'
        }
    },
    {
        name: 'Piros doboz',
        element: 'p',
        attributes: {
            'class':'pirosdoboz'
        }
    },
    {
        name: 'Kép forog jobbra',
        element: 'img',
        attributes: {
            'style': 'margin:20px;-ms-transform:rotate(10deg);-moz-transform:rotate(10deg);-webkit-transform:rotate(10deg);-o-transform:rotate(10deg);transform:rotate(10deg)'
        }
    },
    {
        name: 'Kép forog balra',
        element: 'img',
        attributes: {
            'style': 'margin:20px;-ms-transform:rotate(-8deg);-moz-transform:rotate(-8deg);-webkit-transform:rotate(-8deg);-o-transform:rotate(-8deg);transform:rotate(-8deg)'
        }
    },
    {
        name: 'Szegély nélkül',
        element: 'table',
        styles: { 'border-style': 'hidden' } },
    {
        name: 'kockás lista',
        element: 'ul',
        styles: { 'list-style-type': 'square' } },
    {
        name: 'Vízszintes vonal',
        element: 'hr',
        attributes: {
            'class': 'hr-normal'
        }
    },
    {
        name: 'Gyik cím',
        element: 'h3',
        attributes: {'class': 'toggle'} },
    {
        name: 'Gyik szöveg',
        element: 'div',
        attributes: { 'class': 'toggler'}}
]);

