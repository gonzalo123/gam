dojo.require("dojo.data.ItemFileReadStore");
var strPlaces = {};

dojo.addOnLoad(function(){
    strPlaces.list = new dojo.data.ItemFileReadStore({
        url: "/default/places/list",
        urlPreventCache: 1,
        clearOnClose: true
        }); 
});