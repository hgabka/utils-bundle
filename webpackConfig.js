let filesToCopy = [
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/styles.js', to: 'ckeditor/styles.js'},
    {from: 'node_modules/ckeditor/plugins', to: 'ckeditor/plugins'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/config.js', to: 'ckeditor/config.js'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/contents.css', to: 'ckeditor/contents.css'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/plugins/letterspacing', to: 'ckeditor/plugins/letterspacing'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/plugins/basicstyles', to: 'ckeditor/plugins/basicstyles'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/plugins/simplebutton', to: 'ckeditor/plugins/simplebutton'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/plugins/widgetcommon', to: 'ckeditor/plugins/widgetcommon'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/plugins/removeformat', to: 'ckeditor/plugins/removeformat'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/plugins/lineheight', to: 'ckeditor/plugins/lineheight'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/skins/bootstrapck', to: 'ckeditor/skins/bootstrapck'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/skins/office2003', to: 'ckeditor/skins/office2003'},
    {from: 'vendor/hgabka/utils-bundle/assets/js/ckeditor/skins/v2', to: 'ckeditor/skins/v2'}
];

module.exports = {
    filesToCopy: filesToCopy,
};