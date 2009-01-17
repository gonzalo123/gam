var Cometd = {
    _timeoutInterval: 3000,
    _subscribed : {},
    _subscribedHandle : {},

    subscribe : function(key, handle, first) {
        if (first == true) {
            handle(key);
        }
        Cometd._subscribed[key] = 0;
        Cometd._subscribedHandle[key] = handle;
    },

    createUrl: function(url) {
        var out = '/' + url.module + '/' + url.controller + '/' + url.action;
        if (url.params) {
            var params='';
            for(var index in url.params){
                params += 'key[' + index + ']=' + url.params[index] + '&';
            }
            out += '?' + params;
        }
        return out;
    },

    init : function() {
        var href = Cometd.createUrl({
            module: 'default',
            controller: 'slides',
            action: 'comet',
            params: Cometd._subscribed
        });

        dojo.xhrPost({
            url: href,
            load: function(responseObject, ioArgs){
                if (responseObject['s'] == 1) {
                    // something happens
                    for(var key in responseObject['k']){
                        var handle = Cometd._subscribedHandle[key]
                        var value = responseObject['k'][key];

                        Cometd._subscribed[key] = value;
                        handle(key, value);
                    }
                }
                setTimeout(Cometd.init, Cometd._timeoutInterval);
            },
            error: function(response, ioArgs){
                return response;
            },
            handleAs: "json"
          });
    },
}