(function() {
    tinymce.PluginManager.add('gc_social_wall_button', function( editor, url ) {
        editor.addButton( 'gc_social_wall_button', {
            title: 'GC Social Wall',
            icon: 'gc_cocial_wall',
            onclick: function() {
                editor.insertContent('[gc_social_wall]');
            }
        });
    });
})();