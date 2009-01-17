dojo.require("dojo.data.ItemFileReadStore");
var strBooks = {};

dojo.addOnLoad(function(){
    strBooks.read = new dojo.data.ItemFileReadStore({
        url: "/default/books/list/type/0",
        urlPreventCache: 1,
        clearOnClose: true
        }); 
    strBooks.reading = new dojo.data.ItemFileReadStore({
        url: "/default/books/list/type/1",
        urlPreventCache: 1,
        clearOnClose: true
        }); 
        
    strBooks.toread = new dojo.data.ItemFileReadStore({
        url: "/default/books/list/type/2",
        urlPreventCache: 1,
        clearOnClose: true
        }); 
});