MenuActions = {
    dialogAbout: null,
    dialogCv: null,
    dialogContact: null,
    dialogAbout: null,

    _getDialog: function(title, id) {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'info',
            action: id,
            params: {}
        });
        return new dijit.Dialog({
            id: 'dlg' + id,
            title: title,
            href: href,
            preventCache: false,
            parseOnLoad: true
            });
    },

    contact: function() {
        if (MenuActions.dialogContact == null) {
            MenuActions.dialogContact = MenuActions._getDialog('Contact with Gonzalo', 'contact');
        }
        MenuActions.dialogContact.show();
    },

    cv: function() {
        if (MenuActions.dialogCv == null) {
            MenuActions.dialogCv = MenuActions._getDialog("Gonzalo Ayuso. Resume", 'cv');
        }
        MenuActions.dialogCv.show();
    },

    about: function() {
        if (MenuActions.dialogAbout == null) {
            MenuActions.dialogAbout = MenuActions._getDialog('About', 'about');
        }
        MenuActions.dialogAbout.show();
    },

    aboutApp: function(app, title) {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'index',
            action: 'about' + app,
            params: {}
        });
        if (MenuActions.dialogAbout != null) {
            MenuActions.dialogAbout.destroyRecursive();
        }
        MenuActions.dialogAbout = new dijit.Dialog({
            title: 'about ' + title,
            style: 'width:300px',
            href: href,
            preventCache: true,
            parseOnLoad: true
            });
        MenuActions.dialogAbout.show();
    }
}