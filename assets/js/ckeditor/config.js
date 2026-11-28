/**
 * @license Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */

CKEDITOR.editorConfig = function( config ) {
    config.removeButtons = [];
    // 4.22.1 is the last open source release; without this the editor warns that it is outdated.
    config.versionCheck = false;
};
