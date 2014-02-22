function $ (id) { return document.getElementById(id); }
var formapp = {
	actionHash : "",
	inputs:[],
	salts:{},
	f : null,
	getSalts : function () {
		var a = formapp.inputs.pop(), sf = $("salts");
		while (a) {
			formapp.f[a].setAttribute("readonly");
			formapp.salts[a] = sf[a].value;
			a = formapp.inputs.pop();
		}
	},
	getDataHash : function (data, salt) {
		return MD5(data + salt + formapp.actionHash);
	},
	submit : function () {
		var i, t;
		if ($("sendbutton")){$("sendbutton").remove();}
		formapp.f = $("contactform");
		formapp.actionHash = $("actionhash").value;
		formapp.getSalts();
		for (i in form.salts) {
			if (form.f[i].value!="") {
				formapp.f[i+"hash"].value = formapp.getDataHash(formapp.f[i].value, formapp.salts[i]);
			}
		}
		formapp.f.submit();
	},
	reset : function () {
		var i;
		formapp.f = $("contactform");
		for (i=0;i<formapp.inputs.length;i++) {
			formapp.f[formapp.inputs[i]].value="";
		}
	}
};
