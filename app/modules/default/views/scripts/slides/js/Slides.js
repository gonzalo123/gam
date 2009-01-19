// dojo.place(newNode, 'wrapper', 0);
var Slides = {
    _slides: [],

    _index: 0,

    imageItemStore: undefined,

    handle: function (key, value) {
        //console.log('something changed with ' + key + ' : ' + value);
        dojo.xhrPost({
            url: '/slides/getSlides/key/' + key,
            load: function(responseObject, ioArgs){
                Slides._slides = responseObject;
                for(var index in responseObject){

                    var imgNode = dojo.doc.createElement('img');
                    dojo.attr(imgNode, 'src', Slides._slides[index]);
                    dojo.attr(imgNode, 'style', 'Display:none');
                    dojo.attr(imgNode, 'id', 'pics_' + i);
                    dojo.attr(imgNode, 'class', 'pics');
                    dojo.place(imgNode, 'pics');
                }
                //Slides.slideshow();
            },
            error: function(response, ioArgs){
                return response;
            },
            handleAs: "json"
          });
    },

    slideshow: function() {
        var index = Slides._index;
        Slides.changeImg('img', index);
        Slides._index = index + 1;
        setTimeout(Slides.slideshow, 5000);
    },

    changeImg: function(id, index) {
        dojo.attr(id + '.current', 'src', Slides._slides[index])
        dojo.attr(id + '.next', 'src', Slides._slides[index + 1])
    },

    init: function(key) {
        Slides.handle(key);
        Cometd.subscribe(key, Slides.handle);
        //Cometd.init();
    }
}

dojo.addOnLoad(function(){
    dojo.parser.parse();
    Slides.init('vf1');
});
