var Tabs = {
    tabGroupName: 'tabs',
    _tabs: {},

    tabOnClose: function tabOnClose(id) {
        tabGroup = dijit.byId(Tabs.tabGroupName);
        tabItem = Tabs._tabs[id];

        tabGroup.removeChild(tabItem);
        tabItem.destroyRecursive();
        Tabs._tabs[id] = null;
    },

    addTab: function(id, title, href) {
        if (!Tabs._tabs[id]) {
            var tab = new dijit.layout.ContentPane({
                title: title,
                closable: true,
                onClose: function() {Tabs.tabOnClose(id)},
                href: href
                });
            var tabContainer = dijit.byId(Tabs.tabGroupName);
            tabContainer.addChild(tab);
        } else {
            tab = Tabs._tabs[id];
        }
        dijit.byId(Tabs.tabGroupName).selectChild(tab);
        Tabs._tabs[id] = tab;
    }
}