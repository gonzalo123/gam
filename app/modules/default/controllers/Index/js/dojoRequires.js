dojo.require("dijit.layout.BorderContainer");
dojo.require("dojox.widget.Toaster");
dojo.require("dojo.data.ItemFileReadStore");

dojo.require("dijit.Toolbar");
dojo.require("dijit.form.TextBox");
dojo.require("dijit.form.SimpleTextarea");
dojo.require("dijit.form.DateTextBox");
dojo.require("dijit.form.TimeTextBox");
dojo.require("dijit.form.ComboBox");
dojo.require("dijit.form.Button");
dojo.require("dijit.Menu");
dojo.require("dijit.layout.TabContainer");
dojo.require("dijit.layout.ContentPane");
dojo.require("dijit.form.CheckBox");
dojo.require("dojo.io.script");
dojo.require("dijit.layout.AccordionContainer");



dojo.require("dijit.Dialog");

dojo.require("dojox.grid.DataGrid");

dojo.require("dojo.parser");

var hideLoader = function(){
	dojo.style("preloader", "display", "none");
}
dojo.addOnLoad(function(){
	dojo.parser.parse();
	hideLoader();
});

