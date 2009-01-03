remoteScriptFlag = 'ok';

var Books = {   
    dialogReading: null,
    dialogBook: [],
    addDialog: function() {
        if (Books.dialogReading == null) {
            Books.dialogReading = new dijit.Dialog({ 
                id: 'dialogAdd',
                title: 'Add Reading book', 
                href: '/default/books/addDialog',
                preventCache: true,
                parseOnLoad: true
                });
        }
        Books.dialogReading.show();
    },
    
    saveReading: function(){
        Apps.dialogXhrPost({
            frmName: "frmAddReading",
            url: "/default/books/save/",
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
        Books.dialogBook[id] = new dijit.Dialog({ 
            id: 'book_' + id,
            title: 'Book', 
            href: '/default/books/book/id/' + id,
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
        Apps.dialogXhrPost({
            url: '/default/books/markas/id/' + id + '/status/' + status, 
            dialogName: 'book_' + id,
            cbkOnLoad: function(){
                Books.refreshAllGrids();
            }
        });
    },
    
    editDialog: function(id) {
        Books.dialogBook[id].destroy();
        Books.dialogBook[id] = new dijit.Dialog({ 
            id: 'book_' + id,
            title: 'Book', 
            href: '/default/books/editBook/id/' + id,
            preventCache: true,
            parseOnLoad: true
            });
        Books.dialogBook[id].show();
    },
    
    edit: function(id) {
        Apps.dialogXhrPost({
            frmName: 'edit_book_' + id,
            url: '/default/books/doedit/id/' + id, 
            dialogName: Books.dialogBook[id],
            cbkOnLoad: function() {
                Books.openBookDialog(id);
                Books.refreshAllGrids();
            }
        });
    },
    
    delete: function(id) {
        Apps.dialogXhrPost({
            url: '/default/books/dodelete/id/' + id, 
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
        query = dojo.formToQuery('books.find');
        url = "/default/books/search/?" + query;  
        if (books.luceneresults.store) {
            books.luceneresults.store.close();
        }
        books.luceneresults.store = new dojo.data.ItemFileReadStore({
            url: url,
            urlPreventCache: 1,
            clearOnClose: true,
            });
        books.luceneresults.sort();
    }
}