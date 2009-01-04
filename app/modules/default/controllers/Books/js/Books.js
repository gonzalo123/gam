remoteScriptFlag = 'ok';

var Books = {   
    dialogReading: null,
    dialogBook: [],
    addDialog: function() {
        if (Books.dialogReading == null) {
            var href = Apps.createUrl({
                module: 'default',
                controller: 'books',
                action: 'addDialog',
                params: {}
                });
            Books.dialogReading = new dijit.Dialog({ 
                id: 'dialogAdd',
                title: 'Add Reading book', 
                href: href,
                preventCache: true,
                parseOnLoad: true
                });
        }
        Books.dialogReading.show();
    },
    
    saveReading: function(){
        var href = Apps.createUrl({
            module: 'default',
            controller: 'books',
            action: 'save',
            params: {}
            });
        Apps.dialogXhrPost({
            frmName: "frmAddReading",
            url: href,
            dialogName: 'dialogAdd',
            cbkOnLoad: function() {
                Books.refreshAllGrids();
            }
        });
    },
        
    bookGridDblClick: function(e) {
        var index = e.rowIndex;
        var id = e.grid._by_idx[index].item.id;
        Books.openBookDialog(id);
    },
    
    openBookDialog: function(id) {
        if (Books.dialogBook[id]!=undefined) {
            Books.dialogBook[id].destroy();
        }
        var href = Apps.createUrl({
            module: 'default',
            controller: 'books',
            action: 'book',
            params: {id: id}
            });
        Books.dialogBook[id] = new dijit.Dialog({ 
            id: 'book_' + id,
            title: 'Book', 
            href: href,
            preventCache: true,
            parseOnLoad: true
            });
        
        Books.dialogBook[id].show();
    },
    
    refreshAllGrids: function() {
        Apps.simpleGridRefresh(books.reading);
            Apps.simpleGridRefresh(books.toread);
            Apps.simpleGridRefresh(books.read);
    },
    
    markAs: function(id, status) {
        var href = Apps.createUrl({
                module: 'default',
                controller: 'books',
                action: 'markas',
                params: {id: id, status: status}
                });
        Apps.dialogXhrPost({
            url: href, 
            dialogName: 'book_' + id,
            cbkOnLoad: function(){
                Books.refreshAllGrids();
            }
        });
    },
    
    editDialog: function(id) {
        Books.dialogBook[id].destroy();
        var href = Apps.createUrl({
            module: 'default',
            controller: 'books',
            action: 'editBook',
            params: {id: id}
            });
        Books.dialogBook[id] = new dijit.Dialog({ 
            id: 'book_' + id,
            title: 'Book', 
            href: href,
            preventCache: true,
            parseOnLoad: true
            });
        Books.dialogBook[id].show();
    },
    
    edit: function(id) {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'books',
            action: 'doedit',
            params: {id: id}
            });
        Apps.dialogXhrPost({
            frmName: 'edit_book_' + id,
            url: href, 
            dialogName: Books.dialogBook[id],
            cbkOnLoad: function() {
                Books.openBookDialog(id);
                Books.refreshAllGrids();
            }
        });
    },
    
    delete: function(id) {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'books',
            action: 'dodelete',
            params: {id: id}
            });
        Apps.dialogXhrPost({
            url: href, 
            dialogName: Books.dialogBook[id],
            cbkOnLoad: function() {
                Books.refreshAllGrids();
            }
        });
    },
    
    deleteDialog: function(id) {
        Books.dialogBook[id].destroy();
        Books.dialogBook[id] = new dijit.Dialog({ 
            title: 'Delete', 
            preventCache: true,
            parseOnLoad: true,
        });
        var content = ""; 
        content += "<p>Are you sure?</p>";
        content += "<button dojoType='dijit.form.Button' type='button' onClick='Books.delete("+id+")'>DELETE</button>";
        Books.dialogBook[id].setContent(content);
        Books.dialogBook[id].show();
    },
    
    search: function() {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'books',
            action: 'search',
            params: dojo.formToObject('books.find')
            });
                
        if (books.luceneresults.store) {
            books.luceneresults.store.close();
        }
        books.luceneresults.store = new dojo.data.ItemFileReadStore({
            url: href,
            urlPreventCache: 1,
            clearOnClose: true,
            });
        books.luceneresults.sort();
    }
}