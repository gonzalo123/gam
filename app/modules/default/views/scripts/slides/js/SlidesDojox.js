var Slides = {
    _slides: [],
    _divid: 'slideshow',
    imageItemStore: undefined,

    slideshow: function(key) {
        if (Slides.imageItemStore != undefined) {
            Slides.imageItemStore.close();
        }
        Slides.imageItemStore = new dojo.data.ItemFileReadStore({
            url: '/slides/getSlides/key/' + key,
            urlPreventCache: 1,
            clearOnClose: true,
        });
        dijit.byId(Slides._divid).setDataStore(Slides.imageItemStore, {
            query: {},
        }, {
            imageThumbAttr: "thumb",
            imageLargeAttr: "large"
        }
        );
    }
}

dojo.addOnLoad(function(){
    dojo.parser.parse();
    Cometd.subscribe('vf1', Slides.slideshow, true);
    Cometd.init();
});
