dojo.addOnLoad(function(){
    var onKeyPressFunc = function(event) {
        if(event.ctrlKey && event.keyCode == 'n'){
            Apps.startAppInTab('notes', 'Notes');
            event.preventDefault();
        }
        
    };
    
    dojo.connect(document, "onkeypress", this, onKeyPressFunc);
});