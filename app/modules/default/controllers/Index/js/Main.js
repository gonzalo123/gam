var Main = {
    toaster: function(type, message)
    {
        dijit.byId('toast').setContent(message, type, 5000);
        dijit.byId('toast').show();
    },
    loading :function() {
        dojo.style('logInDiv', "display", "none");
        dojo.style('logOutDiv', "display", "none");
        dojo.style('logInLoading', "display", "inline");
    },
    login: function () {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'auth',
            action: 'dologin',
            params: {}
        });
        Main.loading();
        dojo.xhrPost({
            url: href,
            form: "loginForm",
            load: function(responseObject, ioArgs){
                if (responseObject['status'] == 1) {
                    dojo.style('logInLoading', "display", "none");
                    dojo.style('logInDiv', "display", "none");
                    dojo.style('logOutDiv', "display", "inline");
                    Main.toaster('message', responseObject['txt']);
                    dijit.byId('loginTooltip').onCancel();
                } else if (responseObject['status'] == 0) {
                    dijit.byId('loginTooltip').onCancel();
                    dojo.style('logInLoading', "display", "none");
                    dojo.style('logInDiv', "display", "inline");
                    dojo.style('logOutDiv', "display", "none");
                    Main.toaster('error', responseObject['txt']);
                }
            },
            error: function(response, ioArgs){
                dojo.style('logInDiv', "display", "inline");
                Main.toaster("error", "An error occurred, with response: " + response);
                return response;
            },
            handleAs: "json"
          });
    },
    logout: function () {
        var href = Apps.createUrl({
            module: 'default',
            controller: 'auth',
            action: 'dologoff',
            params: {}
        });
        Main.loading();
        dojo.xhrPost({
            url: href,
            form: "loginForm",
            load: function(responseObject, ioArgs){
                if (responseObject['status'] == 1) {
                    dojo.style('logInLoading', "display", "none");
                    dojo.style('logInDiv', "display", "inline");
                    dojo.style('logOutDiv', "display", "none");
                    Main.toaster('message', responseObject['txt']);
                } else if (responseObject['status'] == 0) {
                    dojo.style('logInLoading', "display", "none");
                    dojo.style('logInDiv', "display", "none");
                    dojo.style('logOutDiv', "display", "inline");
                    Main.toaster('error', responseObject['txt']);
                }
            },
            error: function(response, ioArgs){
                dojo.style('logOutDiv', "display", "inline");
                Main.toaster("error", "An error occurred, with response: " + response);
                return response;
            },
            handleAs: "json"
          });
    },

    sendEmail: function () {
        dojo.xhrPost({
            url: "/index/sendmail/",
            form: "contactFrm",
            load: function(responseObject, ioArgs){
                if (responseObject['status'] == 1) {
                    dojo.byId("contactFrm").reset();
                    Main.toaster('message', responseObject['txt']);
                    if (dijit.byId('dlgcontact')) {
                        dijit.byId('dlgcontact').onCancel();
                    }
                    if (dijit.byId('dlgcontact2')) {
                        dijit.byId('dlgcontact2').onCancel();
                    }
                } else if (responseObject['status'] == 0) {
                    Main.toaster('error', responseObject['txt']);
                }
            },
            error: function(response, ioArgs){
                Main.toaster("error", "An error occurred, with response: " + response);
                return response;
            },
            handleAs: "json"
          });
    },
}