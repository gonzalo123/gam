var remoteScriptValue = null;
var Apps = {
    startAppInTab: function(app, title) 
    {
        var jsUrl = '/default/' + app + '/js/file/init';
        var href  = '/default/' + app + '/index';
        remoteScriptFlag = undefined;
        dojo.io.script.get({
            url: jsUrl, 
            checkString: "remoteScriptFlag",
            load: function(){
                Tabs.addTab(app, title, href);
                remoteScriptFlag = undefined;
            }
        });
    },
    
    dialogXhrPost: function(params) {
        dojo.xhrPost({
            url: params.url,
            form: params.frmName,
            load: function(responseObject, ioArgs){
                if (responseObject['status'] == 1) {
                    if (params.frmName != undefined) {
                        dojo.byId(params.frmName).reset();
                    }
                    Main.toaster('message', responseObject['txt']);
                    dijit.byId(params.dialogName).onCancel();
                    if (params.cbkOnLoad) {
                        params.cbkOnLoad()
                    }
                } else if (responseObject['status'] == 0) { 
                    Main.toaster('error', responseObject['txt']);
                } else if (responseObject['status'] == 9) { 
                    Main.toaster('error', responseObject['txt']);
                    dijit.byId(params.dialogName).onCancel();
                    var prompt = new dijit.Dialog({ 
                        title: 'Delete', 
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
    
    simpleGridRefresh: function (grd)
    {
        grd.store.close();
        grd.sort();
    },
}