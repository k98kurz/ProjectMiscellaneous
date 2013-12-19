function $ (id) { return document.getElementById(id); }

var form = {
	actionHash : "",
	inputs:[],
	salts:{},
	f : null,
	getSalts : function () {
		var a = form.inputs.pop(), sf = $("salts");
		while (a) {
			form.f[a].setAttribute("readonly");
			form.salts[a] = sf[a].value;
			a = form.inputs.pop();
		}
	},
	getDataHash : function (data, salt) {
		return MD5(data + salt + form.actionHash);
	},
	submit : function () {
		var i, t;
		if ($("sendbutton")){$("sendbutton").remove();}
		form.f = $("emailform");
		form.actionHash = $("actionhash").value;
		form.getSalts();
		for (i in form.salts) {
			if (form.f[i].value!="") {
				form.f[i+"hash"].value = form.getDataHash(form.f[i].value, form.salts[i]);
			}
		}
		form.f.submit();
	}
};

function submitForm() { form.submit(); }
