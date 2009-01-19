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
            Books.dialogBook[id].destroyRecursive();
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
            parseOnLoad: true,
            onClose: function() {Books.destroyDialogBook()},
            onCancel : function() {Books.destroyDialogBook()}


            });

        Books.dialogBook[id].show();
    },
    destroyDialogBook: function() {
        for(var id in Books.dialogBook){
            Books.dialogBook[id].destroyRecursive();
        }
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
        Books.dialogBook[id].destroyRecursive();
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
        Books.dialogBook[id].destroyRecursive();
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
    },

    amazonSearch: function (asin) {
        var scriptElement = document.createElement("script");
        scriptElement.setAttribute("id", "jsonScript");
        scriptElement.setAttribute("src", "http://xml-us.amznxslt.com/onca/xml?Service=AWSECommerceService&SubscriptionId=19267494ZR5A8E2CGPR2&AssociateTag=kokogiak7-20&Operation=ItemLookup&Style=http://kokogiak.com/amazon/JSON/ajsonSingleAsin.xsl&ContentType=text/javascript&IdType=ASIN&ItemId=" + asin+ "&ResponseGroup=Large,ItemAttributes,OfferFull&CallBack=Books.amazonCallback");
        scriptElement.setAttribute("type", "text/javascript");
        document.documentElement.firstChild.appendChild(scriptElement);

    },
    amazonCallback: function (booksInfo) {
    },

    googleSearch: function (bibkeys) {
        var scriptElement = document.createElement("script");
        scriptElement.setAttribute("id", "jsonScript");
        scriptElement.setAttribute("src", "http://books.google.com/books?bibkeys=" + bibkeys + "&jscmd=viewapi&callback=Books.googleCallback");
        scriptElement.setAttribute("type", "text/javascript");
        document.documentElement.firstChild.appendChild(scriptElement);

    },

    googleCallback: function (booksInfo) {
        var div = dojo.byId('bookImg');
        div.innerHTML = '';
        var mainDiv = dojo.doc.createElement('div');
        var x = 0;
        for (i in booksInfo) {
            // Create a DIV for each book
            var book = booksInfo[i];
            var thumbnailDiv = dojo.doc.createElement('div');
            thumbnailDiv.className = "thumbnail";

            // Add a link to each book's informtaion page
            var a = dojo.doc.createElement("a");
            a.href = book.info_url;
            a.target = '_blank';

            // Display a thumbnail of the book's cover
            var img = dojo.doc.createElement("img");
            img.src = book.thumbnail_url + '&zoom=1';
            img.border = 0;
            a.appendChild(img);
            thumbnailDiv.appendChild(a);

            mainDiv.appendChild(thumbnailDiv);
        }
        div.appendChild(mainDiv);
    }
}

