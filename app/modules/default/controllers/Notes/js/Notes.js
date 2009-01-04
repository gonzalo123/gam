remoteScriptFlag = 'ok';

var Notes = {   
    dialogAddNote: null,
    dialogNote: null,
    
    addNoteDialog: function() {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'notes',
            action: 'addNoteDialog',
            params: {}
        });
        if (Notes.dialogAddNote == null) {
            Notes.dialogAddNote = new dijit.Dialog({ 
                title: 'Add new Mental Note', 
                id: 'dialogAddNote',
                href: href,
                preventCache: true,
                parseOnLoad: true
                });
        }
        Notes.dialogAddNote.show();
    },
    
    saveNote: function() {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'notes',
            action: 'addnote',
            params: {}
        });
        Apps.dialogXhrPost({
            frmName: "frmAddNote",
            url: addnote,
            dialogName: 'dialogAddNote'
        });
    },
    
    find: function() {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'notes',
            action: 'storeGrd',
            params: dojo.formToObject('form.notes.find')
        });
        
        if (notes.findGrd.store) {
            notes.findGrd.store.close();
        }
        notes.findGrd.store = new dojo.data.ItemFileReadStore({
            url: href,
            urlPreventCache: 1,
            clearOnClose: true,
            });
        notes.findGrd.sort();
    },
    
    gridDblClick: function(e) {
        var index = e.rowIndex;
        var id = e.grid._by_idx[index].item.id;
        Notes.openNoteDialog(id);
    },
    
    openNoteDialog: function(id) {
        if (Notes.dialogNote != null) {
            Notes.dialogNote.destroy();
        }
        var href = Apps.createUrl({
            module: 'default',
            controller: 'notes',
            action: 'note',
            params: {id: id}
        });
        
        Notes.dialogNote = new dijit.Dialog({ 
            title: 'Add new Mental Note', 
            href: href,
            preventCache: true,
            parseOnLoad: true
            });
        Notes.dialogNote.show();
    },
    
    edit: function(id) {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'notes',
            action: 'doedit',
            params: {}
        });
        Apps.dialogXhrPost({
            frmName: 'notes.formNote_' + id,
            url: href, 
            dialogName: Notes.dialogNote,
            cbkOnLoad: function() {
                Notes.find();
            }
        });
    },
    
    deleteDialog: function(id) {
        Notes.dialogNote.destroy();
        Notes.dialogNote = new dijit.Dialog({ 
            title: 'Delete', 
            preventCache: true,
            parseOnLoad: true,
        });
        var content = ""; 
        content += "<p>Are you sure?</p>";
        content += "<button dojoType='dijit.form.Button' type='button' onClick='Notes.delete("+id+")'>DELETE</button>";
        Notes.dialogNote.setContent(content);
        Notes.dialogNote.show();
    },
    
    delete: function(id) {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'notes',
            action: 'dodelete',
            params: {id: id}
        });
        
        Apps.dialogXhrPost({
            url: href, 
            dialogName: Notes.dialogNote,
            cbkOnLoad: function() {
                Notes.find();
            }
        });
    },
    
}