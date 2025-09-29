/**
 * @license Copyright (c) 2003-2023, CKSource Holding sp. z o.o. All rights reserved.
 * For licensing, see https://ckeditor.com/legal/ckeditor-oss-license
 */
CKEDITOR.editorConfig = function(config) {
    config.allowedContent = true;
    // Define changes to the default configuration here.
    config.extraPlugins = 'uploadimage,image,video,clipboard,table,justify,codesnippet,font,colorbutton'; // Add plugins as needed
    config.toolbarGroups = [
        { name: 'clipboard', groups: [ 'clipboard', 'undo' ] },
        { name: 'editing', groups: [ 'find', 'selection', 'editing' ] },
        { name: 'links', groups: [ 'links' ] },
        { name: 'insert', groups: [ 'insert', 'video' ] },
        { name: 'forms', groups: [ 'forms' ] },
        { name: 'tools', groups: [ 'tools' ] },
        { name: 'document', groups: [ 'mode', 'document', 'doctools' ] },
        { name: 'others', groups: [ 'others' ] },
        '/',
        { name: 'basicstyles', groups: [ 'basicstyles', 'cleanup' ] },
        { name: 'paragraph', groups: [ 'align', 'list', 'indent', 'blocks', 'bidi', 'paragraph', 'JustifyLeft', 'JustifyCenter', 'JustifyRight', 'JustifyBlock' ] }, // Add more groups as needed. ] },
        { name: 'styles', groups: [ 'styles' ] },
        { name: 'colors', groups: [ 'colors' ] }
    ];

    // Ensure toolbar includes specific buttons for image, video, and alignment.
    config.removeButtons = 'Underline,Subscript,Superscript';  // Remove "Subscript" and "Superscript" buttons
    config.format_tags = 'p;h1;h2;h3;pre';
    config.removeDialogTabs = 'image:advanced;link:advanced';
    config.height = 300;

    // File browser settings (if using Laravel File Manager).
    // config.filebrowserBrowseUrl = '/admin/laravel-filemanager?editor=ckeditor&type=Files';
    // config.filebrowserImageBrowseUrl = '/admin/laravel-filemanager?editor=ckeditor&type=Images';
    // config.filebrowserUploadUrl = '/admin/laravel-filemanager/upload?editor=ckeditor&type=Files&_token=' + document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // config.filebrowserImageUploadUrl = '/admin/laravel-filemanager/upload?editor=ckeditor&type=Images&_token=' + document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    // config.uploadUrl = '/admin/laravel-filemanager/upload?editor=ckeditor&type=Files';
    // config.removePlugins = 'image2';

    config.attributes = [ 'left', 'center', 'right' ];
    config.font_names = 'Arial;Times New Roman;Verdana;Tahoma;Courier New;Roboto;Georgia;Comic Sans MS;Impact;Lucida Sans Unicode;Palatino Linotype;Trebuchet MS;Helvetica';
    config.fontSize_sizes = '8/8px;10/10px;12/12px;13/13px;14/14px;16/16px;20/20px;24/24px;36/36px';
    config.fontSize_input = true; // Enable custom font size input

    // Add color buttons to the toolbar
    config.colorButton_enableAutomatic = true;
    config.colorButton_enableMore = true;
    config.colorButton_colors = '000,800000,8B4513,2F4F4F,008080,000080,4B0082,696969,' +
                                'B22222,A52A2A,DAA520,006400,40E0D0,0000CD,800080,808080,' +
                                'F00,FF8C00,FFD700,008000,0FF,00F,EE82EE,A9A9A9,' +
                                'FFA07A,FFA500,FFFF00,00FF00,AFEEEE,ADD8E6,DDA0DD,D3D3D3,' +
                                'FFF0F5,FAEBD7,FFFFE0,F0FFF0,F0FFFF,F0F8FF,E6E6FA,FFF';
    config.colorButton_foreStyle = {
        element: 'span',
        styles: { color: '#(color)' },
        overrides: [ { element: 'font', attributes: { 'color': null } } ]
    };
    config.colorButton_backStyle = {
        element: 'span',
        styles: { 'background-color': '#(color)' }
    };
};