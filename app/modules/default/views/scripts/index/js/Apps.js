var remoteScriptValue = null;
var Apps = {
    createUrl2: function(url) {
        var out = '?' +
            'module=' + url.module + '&' +
            'controller=' + url.controller + '&' +
            'action=' + url.action;
        if (url.params) {
            for(var index in url.params){
                out += '&' + index + '=' + url.params[index];
            }
        }
        return out;
    },
    createUrl: function(url) {
        var out = '/' + url.module + '/' + url.controller + '/' + url.action;
        if (url.params) {
            for(var index in url.params){
                out += '/' + index + '/' + url.params[index];
            }
        }
        return out;
    },

    startAppInTab: function(app, title)
    {
        var jsUrl = Apps.createUrl({
            module: 'default',
            controller: app,
            action: 'js',
            params: {
                file: 'init'
            }
        });

        var href = Apps.createUrl({
            module: 'default',
            controller: app,
            action: 'index',
            params: {}
        });


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
                        parseOnLoad: true
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
    }
}