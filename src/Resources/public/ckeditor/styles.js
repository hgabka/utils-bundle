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

	{ name: 'Italic Title',		element: 'h2', styles: { 'font-style': 'italic' } },
	{ name: 'Subtitle',			element: 'h3', styles: { 'color': '#aaa', 'font-style': 'italic' } },
	{
		name: 'Special Container',
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

	{ name: 'Big',				element: 'big' },
	{ name: 'Small',			element: 'small' },
	{ name: 'Typewriter',		element: 'tt' },

	{ name: 'Computer Code',	element: 'code' },
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
		name: 'Styled image (left)',
		element: 'img',
		attributes: { 'class': 'left' }
	},

	{
		name: 'Styled image (right)',
		element: 'img',
		attributes: { 'class': 'right' }
	},

	{
		name: 'Compact table',
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

	{ name: 'Borderless Table',		element: 'table',	styles: { 'border-style': 'hidden', 'background-color': '#E6E6FA' } },
	{ name: 'Square Bulleted List',	element: 'ul',		styles: { 'list-style-type': 'square' } },
 {
name: 'Marketing Főcím',
element: 'h1',
attributes: {
'class':'salesH1'
}
},
{
name: 'Marketing alcím',
element: 'h2',
attributes: {
'class':'salesH2black'
}
},
{
name: 'Csekklista',
element: 'li',
attributes: {
'class':'csekklista'
}
},
{
name: 'Csekklista 2',
element: 'li',
attributes: {
'class':'csekklista2'
}
},
{
name: 'Csekklista zöld',
element: 'li',
attributes: {
'class':'csekklistazold'
}
},
{
name: 'X lista',
element: 'li',
attributes: {
'class':'lista-x'
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
name: 'Kerek gomb lila',
element: 'a',
attributes: {
'class':'kerekgomb'
}
},
{
name: 'Nagygomb kék',
element: 'a',
attributes: {
'class':'nagygombkek'
}
},
{
name: 'Nagygomb kék 2',
element: 'a',
attributes: {
'class':'nagygombkek2'
}
},
{
name: 'Nagygomb fehér',
element: 'a',
attributes: {
'class':'nagygombfeher'
}
},
{
name: 'Nagygomb narancs',
element: 'a',
attributes: {
'class':'nagygombnarancs'
}
},
{
name: 'Nagygomb narancs 2',
element: 'a',
attributes: {
'class':'nagygombnarancs2'
}
},
{
name: 'Nagygomb világoskék',
element: 'a',
attributes: {
'class':'nagygombvilagoskek'
}
},
{
name: 'Nagygomb piros',
element: 'a',
attributes: {
'class':'nagygombpiros'
}
},
{
name: 'Nagygomb sötétzöld',
element: 'a',
attributes: {
'class':'nagygombsotetzold'
}
},
{
name: 'Nagygomb lila',
element: 'a',
attributes: {
'class':'nagygomblila'
}
},
{
name: 'Nagygomb fekete',
element: 'a',
attributes: {
'class':'nagygombfekete'
}
},
{
name: 'Nagygomb világos zöld',
element: 'a',
attributes: {
'class':'nagygombvilagoszold'
}
},
{
name: 'Kisgomb fehér',
element: 'a',
attributes: {
'class':'superbutton white'
}
},
{
name: 'Kisgomb fekete',
element: 'a',
attributes: {
'class':'superbutton black'
}
},
{
name: 'Kisgomb sárga',
element: 'a',
attributes: {
'class':'superbutton yellow'
}
},
{
name: 'Kisgomb piros',
element: 'a',
attributes: {
'class':'superbutton red'
}
},
{
name: 'Kisgomb neonkék',
element: 'a',
attributes: {
'class':'superbutton neon'
}
},
{
name: 'Kisgomb lila',
element: 'a',
attributes: {
'class':'superbutton pink'
}
},
{
name: 'Kisgomb szürke',
element: 'a',
attributes: {
'class':'superbutton friendly_grey'
}
},
{
name: 'Kisgomb narancs',
element: 'a',
attributes: {
'class':'superbutton orange'
}
},
{
name: 'Kisgomb zöld',
element: 'a',
attributes: {
'class':'superbutton green'
}
},
{
name: 'Kisgomb lágy zöld',
element: 'a',
attributes: {
'class':'superbutton soft_green'
}
},
{
name: 'Kisgomb kórház zöld',
element: 'a',
attributes: {
'class':'superbutton hospital_green'
}
},
{
name: 'Kisgomb koralkék',
element: 'a',
attributes: {
'class':'superbutton coral'
}
},
{
name: 'Kisgomb bíbor',
element: 'a',
attributes: {
'class':'superbutton purple'
}
},
{
name: 'Kisgomb lágy kék',
element: 'a',
attributes: {
'class':'superbutton soft_teal'
}
},
{
name: 'Kisgomb szelén',
element: 'a',
attributes: {
'class':'superbutton selen'
}
},
{
name: 'Kisgomb bronz',
element: 'a',
attributes: {
'class':'superbutton bronze'
}
},
{
name: 'Marketing doboz',
element: 'p',
attributes: {
'class':'salesSiker'
}
},
{
name: 'Marketing doboz kék',
element: 'p',
attributes: {
'class':'salesalja'
}
},
{
name: 'Képkeret',
element: 'img',
attributes: {
'class':'keret'
}
},
{
name: 'Képkeret és árnyék',
element: 'img',
attributes: {
'class':'keretarnyek'
}
},
{
name: 'Kép balra',
element: 'img',
attributes: {
'class': 'alignleft keret'
}
},
{
name: 'Kör alakú kép',
element: 'img',
attributes: {
'class': 'kerek'
}
},
{
name: 'Kör alakú kép árnyékkal',
element: 'img',
attributes: {
'class': 'kerekarnyek'
}
},
{
name: 'Kép jobbra',
element: 'img',
attributes: {
'class': 'alignright keret'
}
},{
name: 'Kép forog jobbra',
element: 'img',
attributes: {
'style': 'margin:20px;-ms-transform:rotate(10deg);-moz-transform:rotate(10deg);-webkit-transform:rotate(10deg);-o-transform:rotate(10deg);transform:rotate(10deg)'
}
},{
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
name: 'Gyik cím',
element: 'h3',
attributes: {'class': 'toggle'} },
{
name: 'Gyik szöveg',
element: 'div',
attributes: { 'class': 'toggler'}}
]);

