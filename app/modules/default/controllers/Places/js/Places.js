remoteScriptFlag = 'ok';

var Places = {
    mainMap: null,
    addMap: null,
    dialogAdd: null, 
    addMarker: null,
    dialogEdit: null,
    marks: [], 
    
    loadMarks: function() {
        dojo.xhrPost({
            var href = Apps.createUrl({
                module: 'default',
                controller: 'places',
                action: 'getplaces',
                params: {}
            });
            url: href, 
            load: function(responseObject, ioArgs){
                dojo.forEach(Places.marks, function(item) {
                    Places.mainMap.removeOverlay(item);
                });
                dojo.forEach(responseObject, function(item, index) {
                    marker = Places.createInfoMarker(new GLatLng(item.lat, item.lng), item.title, item.body, item.id);
                    Places.mainMap.addOverlay(marker); 
                    Places.marks[index] = marker;
                });
            },
            error: function(response, ioArgs){
                Main.toaster("error", "An error occurred, with response: " + response);
                return response;
            },
            handleAs: "json"
          });
    },
    
    createInfoMarker: function (point, title, body, id) {
        var marker = new GMarker(point);
        var txt = "<table border='0' cellpadding='0' cellspacing='0'><tr><td>"+title+"</td></tr><tr><td>"+body+"</td></tr></table>";
        txt += "<table width='100%'><tr><td align='left'><button onClick='Places.deletePlace("+ id +")'>Delete</button></td>";
        txt += "<td align='right'><button onClick='Places.editDialogPlace("+ id +")'>Edit</button></td></tr></table>";
        GEvent.addListener(marker, "click", function() { 
            marker.openInfoWindowHtml(txt); 
        });
        return marker;
    },
    
    gloadAdd: function() {
        if (GBrowserIsCompatible()) {
            Places.addMap = new GMap2(document.getElementById("mapAdd"));
            Places.addMap.setMapType(G_HYBRID_MAP);
            Places.addMap.setCenter(new GLatLng(43.3184, -1.99331), 13);
            Places.addMap.addControl(new GLargeMapControl());
            Places.addMap.addControl(new GMapTypeControl());
            Places.addMap.enableScrollWheelZoom(true);
            GEvent.addListener(Places.addMap, "click", function(overlay,point) { 
                if (Places.addMarker) {
                    Places.addMap.removeOverlay(Places.addMarker);
                }
                Places.addMarker = new GMarker(point);
                Places.addMap.addOverlay(Places.addMarker); 
            });
        }
    },
    
    gload: function() {
        if (GBrowserIsCompatible()) {
            Places.mainMap = new GMap2(document.getElementById("map"));
            Places.mainMap.setMapType(G_HYBRID_MAP);
            Places.mainMap.setCenter(new GLatLng(43.3184, -1.99331), 13);
            Places.mainMap.addControl(new GLargeMapControl());
            Places.mainMap.addControl(new GMapTypeControl());
            Places.mainMap.enableScrollWheelZoom(true);
            Places.loadMarks();
        }
    },
    
    gridDblClick: function(e) {
        var index = e.rowIndex;
        var lat = e.grid._by_idx[index].item.lat;
        var lng = e.grid._by_idx[index].item.lng;
        Places.mainMap.setCenter(new GLatLng(lat, lng), 13);
    },
    
    savePlace: function(){
        var point = Places.addMarker.getPoint();
        var href = Apps.createUrl({
                module: 'default',
                controller: 'places',
                action: 'save',
                params: {
                    lat: point.lat(),
                    lng: point.lng()
                }
            });
        Apps.dialogXhrPost({
            frmName: "frmAddPlace",
            url: href,
            dialogName: Places.dialogAdd,
            cbkOnLoad: function() {
                Apps.simpleGridRefresh(places.grd);
                Places.loadMarks();
            }
        });
    },
    
    editDialogPlace: function(id) {
        var href = Apps.createUrl({
                module: 'default',
                controller: 'places',
                action: 'editDialog',
                params: {
                    id: id,
                }
            });
            
        if (Places.dialogEdit == null) {
            Places.dialogEdit = new dijit.Dialog({ 
                title: 'Edit Place',
                href: href,
                preventCache: true,
                parseOnLoad: true
                });
        }
        Places.dialogEdit.show();
    },
    
    deletePlace: function(id) {
        var href = Apps.createUrl({
                module: 'default',
                controller: 'places',
                action: 'delete',
                params: {
                    id: id,
                }
            });
         dojo.xhrPost({
            url: href, 
            load: function(responseObject, ioArgs){
                if (responseObject['status'] == 1) {
                    Main.toaster('message', responseObject['txt']);
                    Apps.simpleGridRefresh(places.grd);
                    Places.loadMarks();
                } else if (responseObject['status'] == 0) { 
                    Main.toaster('error', responseObject['txt']);
                } else if (responseObject['status'] == 9) { 
                    Main.toaster('error', responseObject['txt']);
                    var prompt = new dijit.Dialog({ 
                        preventCache: true,
                        parseOnLoad: true,
                    });
                    var content = ""; 
                    content += "<p>"+responseObject['txt']+"</p>";
                    prompt.setContent(content);
                    prompt.show();
                }
            },
            error: function(response, ioArgs){
                Main.toaster("error", "An error occurred, with response: " + response);
                return response;
            },
            handleAs: "json"
          });
         
    },
     
    add: function() {
        if (Places.addMarker) {
            Places.addMap.removeOverlay(Places.addMarker);
        }
        var href = Apps.createUrl({
            module: 'default',
            controller: 'places',
            action: 'addDialog',
            params: {}
            });
        if (Places.dialogAdd == null) {
            Places.dialogAdd = new dijit.Dialog({ 
                title: 'Add Place',
                style: 'width:650px;height:450px',
                href: href,
                preventCache: true,
                parseOnLoad: true
                });
        }
        Places.dialogAdd.show();
    },
    
    editPlace: function() {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'places',
            action: 'edit',
            params: {}
            });
        Apps.dialogXhrPost({
            frmName: "frmEditPlace",
            url: href, 
            dialogName: Places.dialogEdit,
            cbkOnLoad: function() {
                Apps.simpleGridRefresh(places.grd);
                Places.loadMarks();
            }
        });
    }
}