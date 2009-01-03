remoteScriptFlag = 'ok';

var Notes = {   
    dialogAddNote: null,
    dialogNote: null,
    
    addNoteDialog: function() {
        if (Notes.dialogAddNote == null) {
            Notes.dialogAddNote = new dijit.Dialog({ 
                title: 'Add new Mental Note', 
                id: 'dialogAddNote',
                href: '/default/notes/addNoteDialog',
                preventCache: true,
                parseOnLoad: true
                });
        }
        Notes.dialogAddNote.show();
    },
    
    saveNote: function() {
        Apps.dialogXhrPost({
            frmName: "frmAddNote",
            url: "/default/notes/addnote/",
            dialogName: 'dialogAddNote'
        });
    },
    
    find: function() {
        query = dojo.formToQuery('form.notes.find');
        url = "/default/notes/storeGrd/?" + query;    
        if (notes.findGrd.store) {
            notes.findGrd.store.close();
        }
        notes.findGrd.store = new dojo.data.ItemFileReadStore({
            url: url,
            urlPreventCache: 1,
            clearOnClose: true,
            });
        notes.findGrd.sort();
    },
    
    gridDblClick: function(e) {
        console.log(e);
        var index = e.rowIndex;
        var id = e.grid._by_idx[index].item.id;
        Notes.openNoteDialog(id);
    },
    
    openNoteDialog: function(id) {
        if (Notes.dialogNote != null) {
            Notes.dialogNote.destroy();
        }
        Notes.dialogNote = new dijit.Dialog({ 
            title: 'Add new Mental Note', 
            href: '/default/notes/note/id/' + id,
            preventCache: true,
            parseOnLoad: true
            });
        Notes.dialogNote.show();
    },
    
    edit: function(id) {
        Apps.dialogXhrPost({
            frmName: 'notes.formNote_' + id,
            url: '/default/notes/doedit/', 
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
        Apps.dialogXhrPost({
            url: '/default/notes/dodelete/id/' + id, 
            dialogName: Notes.dialogNote,
            cbkOnLoad: function() {
                Notes.find();
            }
        });
    },
    
}