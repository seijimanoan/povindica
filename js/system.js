window["doDebug"] =
{	target: $("#debug_outputs_in_here"),
	add: function (sort, title, content)
	{	this.target.prepend ("<div class=\"well\"><div class=\"alert alert-"+(sort==1?"info":"danger")+"\">["+doTime.now()+"] "+title+"</div>"+content+"</div>");
		$("#system_show_debug").modal ("show");
		if (sort == 1) setTimeout (function (){ $("#system_show_debug").modal ("hide"); }, 1000);
	},
	clear: function ()
	{ this.target.html (""); },
	bold: function (entry)
	{ return "<code>"+entry+"</code>"; }
};
window["doTime"] =
{	now: function ()
	{	var ourTime = new Date ();
		var output = ((ourTime.getHours()<10?"0":"")+ourTime.getHours())+":"+((ourTime.getMinutes()<10?"0":"")+ourTime.getMinutes());
		return output;
	}
};
window["hookButton"] = function ()
{	$("[href^='#go']").off().on
	("click", function (e)
	{	e.preventDefault ();
		window.routeData.local (this);
		
		if ($(this).closest(".nav").length)
		{	$(this).closest("li").parent().find("li[class!='divider']").attr ("class", "");
			$(this).closest("li").attr ("class", "active");
		}
	});
};
window["routeData"] =
{	last: "",
	mod: "",
	act: "",
	opt: {},
	url: function (target)
	{	var handler = "#!/",
			filtered = target ? target.replace (/^[\^|\#|\!|\/|\W]+/g, "") : false,
			url = handler+(filtered ? filtered : "goHome"),
			sharped = (filtered ? "#" : "")+filtered,
			struct = filtered ? filtered.split("/") : [""],
			module = filtered ? struct[0] : "",
			actions = filtered ? struct[1] : [""],
			options = filtered ? struct.slice(2) : {},
			assign = function ()
			{	return (function ()
				{	if (history.pushState) window.history.pushState ("", document.title, window.location.pathname+window.location.search+url);
					else window.location.hash = url;
				});
			},
			print = function (entry)
			{	return (function (entry)
				{	window.location.hash = handler+window.routeData.mod+"/"+entry;
				});
			};
		return {"uri": url, "target": filtered, "sharp": sharped, "path": struct, "page": module, "onto": actions, "arg": options, "up": handler, "assign": assign (), "echo": print ()};
	},
	local: function (t)
	{	this.url($(t).attr("href")+($(t).attr("data-search")?("/"+$(t).attr("data-search")):"")).assign ();
		var current = typeof $(t).attr("data-search") == "undefined" ? $(t).attr("href") : $(t).attr("data-search");
		
		if (true)
		{	this.last = current;
			this.script ("local");
		}
		
		return false;
	},
	remote: function ()
	{	$("a[href='#']").each (function (i) { $(this).attr ("href", "#!/"); });
		this.url(window.location.hash).assign ();
		
		if (localStorage.getItem ("login-response") && localStorage.getItem ("login-response") == "success")
		{	sessionStorage.setItem ("last-request", "");
			this.script ("remote");
		}
		else
		{	sessionStorage.setItem ("last-request", window.location.hash);
			window.routeData.local ({"href": "#goHome"});
		}
		
		return false;
	},
	script: function (environment)
	{	if (this.url(window.location.hash).target.length !== -1)
		{	this.mod = escape (this.url(window.location.hash).page);
			this.act = this.url(window.location.hash).onto;
			this.opt = this.url(window.location.hash).arg;

			if (this.mod && this.mod.length > 0 && sessionStorage.getItem ("last-module") !== this.mod)
			{	if (typeof window.syncData === "object")
				{	delete window.syncData;
					window.syncData = null;
				}

				this.load ("./css/__"+this.mod+".css", "css");
				this.load ("./rsrc/__"+this.mod+".php", "html");
				this.load ("./js/__"+this.mod+".js", "js");

				$.when (window.routeData.template ()).then (function ()
				{	if (typeof window.syncData === "object")
					{	window.syncData.init_construct ();
						window.routeData.actions ();
					}
					window.hookButton ();
				});

				window.sessionStorage.setItem ("last-module", this.mod);
				window.sessionStorage.setItem ("last-action", "");
			}
			else
			{	window.routeData.actions ();
				window.hookButton ();
			}
		}
		window.hookButton ();
	},
	template: function ()
	{	return $.ajax
		({ url: "./rsrc/__"+window.routeData.mod+".php", success: function (e) { $("#__"+window.routeData.mod).html (e); } })
		.error(function () { $.ajax ({ url: "./error.php?calling="+(window.routeData.mod?window.routeData.mod:""), success: function (e) { $("#htmlView").html (e); window.hookButton (); } }); });
	},
	design: function ()
	{	var aliases = ["method", "object", "this", "params"], owned = [], limit = "@";
		var opt = {params: {}, uri: ""};
		var _ = function (entry)
		{	var newer = "", entry = unescape (entry);
			for (var n = 0; n < entry.length; n++) newer += escape(entry[n]).replace (/%[0-9A-Za-z]*/g, '');
			return newer;
		};
		
		// Treat one for parameter options
		for (var n  in this.opt)
		if (n >= aliases.indexOf ("params") && this.opt[n].length > 0)
		{	owned = this.opt[n].split (limit);
			opt["params"][owned[0]] = owned.slice(1).join (limit);
			delete owned;
			
			opt["uri"] += this.opt[n].search ('page@') == 0 ? "" : ("/"+this.opt[n]);
		}
		else if (n < aliases.indexOf ("params"))
			this.opt[(aliases[n] === undefined ? n : _ (aliases[n]))] = _ (this.opt[n]);
		else
			delete this.opt[n];
		
		// Treat two for parameter options
		for (var o in opt) this.opt[o] = typeof opt[o] == "object" ? opt[o] : opt[o];
		
		// Treated parameter options
		return this.opt;
	},
	actions: function ()
	{	
		if (window.routeData.act && window.routeData.act.length > 0 && window.localStorage.getItem ("login-response") == "success")
		{	if (typeof window.syncData.init_general()[window.routeData.act] == "function")
			{	window.syncData.init_general()[window.routeData.act] (this.design ());
				window.sessionStorage.setItem ("last-action", window.routeData.act);
			}
			
			$("a[href='#"+window.routeData.act+"']").tab ("show");
			
			if ($("#"+window.routeData.act).is (":hidden"))
			$("#"+window.routeData.act).show ();
			
			return true;
		}
		return false;
	},
	load: function (filename, filetype)
	{	var target = filetype+"View";
		if (filetype == "js")
		{	$("#"+target).remove ();
			var fileref = document.createElement ('script');
			fileref.setAttribute ("type", "text/javascript");
			fileref.setAttribute ("id", target);
			fileref.setAttribute ("src", filename);
			$("head").append (fileref);
			return true;
		}
		else if (filetype == "css")
		{	$("#"+target).remove ();
			var fileref = document.createElement ("link");
			fileref.setAttribute ("rel", "stylesheet");
			fileref.setAttribute ("type", "text/css");
			fileref.setAttribute ("media", "screen");
			fileref.setAttribute ("id", target);
			fileref.setAttribute ("href", filename);
			$("head").append (fileref);
			return true;
		}
		else if (filetype == "html")
		{	var fileref = document.createElement ("div");
			fileref.setAttribute ("id", "__"+this.mod);
			$("section#"+target).html (fileref);
			return true;
		}
		else return false;
	}
}
function __rc4 (key, str)
{	var s = [], j = 0, x, res = '';
	for (var i = 0; i < 256; i++) s[i] = i;
	for (i = 0; i < 256; i++)
	{	j = (j + s[i] + key.charCodeAt(i % key.length)) % 256;
		x = s[i];
		s[i] = s[j];
		s[j] = x;
	}
	i = j = 0;
	for (var y = 0; y < str.length; y++)
	{	i = (i + 1) % 256;
		j = (j + s[i]) % 256;
		x = s[i];
		s[i] = s[j];
		s[j] = x;
		res += String.fromCharCode(str.charCodeAt(y) ^ s[(s[i] + s[j]) % 256]);
	}
	return res;
}
window["sysAuth"] =
{	menubar:
	{	__construct: function ()
		{	return $.ajax
			({	type: window.syncData.current_method,
				url: "data/__goHome.php",
				dataType: window.syncData.current_object,
				context: this,
				data:
				{	"go": window.syncData.current_object,
					"on": "getRuledMenu"
				},
				success: function (entry)
				{	this.load (entry);
					window.hookButton ();
					window.console.log ("Authentication");
				}
			});
		},
		dom: function ()
		{	return {
				space: $("#user-menu-bar"),
				further: $("#etc_long_menubar"),
				expanse: $("#etc_long")
			}
		},
		load: function (entry)
		{	this.dom().space.html ("<li class=\""+(sessionStorage.getItem("last-module")=="goHome"?"active":"")+"\"><a href=\"#goHome\">Principal</a></li>");
			if (entry && entry.length > 3)
				this.dom().expanse.html ('<li class="dropdown"><a href="" class="dropdown-toggle" data-toggle="dropdown">Mais <b class="caret"></b></a><ul id="etc_long_menubar" class="dropdown-menu"></ul></li>');
			else if (this.dom().expanse.length > 0)
				this.dom().expanse.empty ();

			for (var n in entry)
			if (n < 3) this.dom().space.append ("<li class=\""+(sessionStorage.getItem("last-module")==entry[n].modular?"active":"")+"\"><a href=\"#"+entry[n].modular+"\">"+entry[n].name+"</a></li>");
			else this.dom().further.append ("<li class=\""+(sessionStorage.getItem("last-module")==entry[n].modular?"active":"")+"\"><a href=\"#"+entry[n].modular+"\">"+entry[n].name+"</a></li>");
		}
	},
	login: function ()
	{	return $.ajax
		({	type: window.syncData.current_method,
			url: "data/__goHome.php",
			dataType: window.syncData.current_object,
			context: this,
			data:
			{	"go": window.syncData.current_object,
				"on": "getLogin",
				"resu": sysAuth.dom().login_user (),
				"ssap": encodeURIComponent (btoa (__rc4 (sysAuth.dom().login_data (), sysAuth.dom().login_pass ())))
			},
			success: function ()
			{	this.logon ();
			}
		});
	},
	logon: function ()
	{	$.ajax
		({	type: window.syncData.current_method,
			url: "data/__goHome.php",
			dataType: window.syncData.current_object,
			context: this,
			data:
			{	"go": window.syncData.current_object,
				"on": "getLogon"
			},
			success: function (entry)
			{	if (entry.response === "error")
				{	window.sysAuth.dom().login_alert.html ('<div class="alert alert-danger">'+entry.desc+'</div>');
					$("#mod_prest_details").modal ("show");
					$("#mod_prest_details .form-group").attr ("class", "form-group has-error");
					$("#login_exit").attr ("disabled", true);
					window.localStorage.setItem ("login-response", "error");
					window.trafficBox.show ("Nenhuma autenticação para prosseguir.");
				}
				else
				{	window.sysAuth.dom().login_alert.html ('<div class="alert alert-info">'+entry.desc+'</div>');
					$("#mod_prest_details").modal ("hide");
					$("#mod_prest_details .form-group").attr ("class", "form-group");
					$("#login_exit").attr ("disabled", false);
					window.localStorage.setItem ("login-response", "success");
					window.trafficBox.hide ();
					/* inits */
					this.menubar.__construct ();
					window.syncData.__construct ();
				}
				$("#login_data").val (entry.data);
				window.sysAuth.dom().login_pass ("^");
				$("#login_pass").off().on ("keypress", function (e)
				{	if (e.keyCode == 13) window.sysAuth.login ();
				});
				delete entry;
			},
			failure: function ()
			{	window.sysAuth.dom().login_alert.html ("<div class=\"alert alert-danger\">Algum erro ocorreu, verifique a sua conexão e tente novamente</div>");
				$("#mod_prest_details").modal ("show");
				window.localStorage.setItem ("login-response", "error");
			}
		});
		return localStorage.getItem ("login-response");
	},
	logout: function ()
	{	return $.ajax
		({	type: window.syncData.current_method,
			url: window.syncData.controller_path,
			dataType: window.syncData.current_object,
			context: this,
			data:
			{	"go": window.syncData.current_object,
				"on": "getLogout"
			},
			success: function ()
			{	window.localStorage.setItem ("login-response", "error");
				window.location = window.location.protocol+"//"+window.location.host+"/atendimento";
			}
		});
	},
	dom: function ()
	{	return {
			"login_user": function (s){ if (s == "^"){ $("#login_user").val (""); } else return $("#login_user").val (); },
			"login_pass": function (s){ if (s == "^"){ $("#login_pass").val (""); } else return $("#login_pass").val (); },
			"login_data": function (s){ if (s == "^"){ $("#login_data").val (""); } else return $("#login_data").val (); },
			"login_alert": $("#login_alert")
		}
	},
	hook: (function ()
	{	$("#login_entrance").off().on
		("click", function (e)
		{	e.preventDefault ();
			window.sysAuth.login ();
		});
		$("#login_exit").off().on
		("click", function (e)
		{	e.preventDefault ();
			window.sysAuth.logout ();
		});
	})()
};
window["trafficBox"] =
{	progress:
	{	danger: "progress-bar progress-bar-danger",
		simple: "progress-bar"
	},
	element: function (i)
	{	return $("[data-loading-text][data-object='"+i["this"]+"'][data-method^='"+i["object"]+"-']");
	},
	warn: "Carregando, por favor aguarde...",
	show: function (message)
	{	if (typeof message === "object" && this.element (message) !== undefined) $.each (this.element (message), function () { if (typeof $(this).data ("loading-text")  !== "undefined") $(this).button ("loading"); });
		else
		{	if ($("#blured-loading").is(':hidden')) $("#blured-loading").show ();
			$("#blured-text-message").text ((message?message:this.warn));
			$("#blured-progress").attr ("class", (localStorage.getItem ("login-response") === "success" ? this.progress.simple : this.progress.danger));
		}
	},
	hide: function (message)
	{	if (message !== undefined && this.element (message) !== undefined) $.each (this.element (message), function () { if (typeof $(this).data ("loading-text")  !== "undefined") $(this).button ("reset"); });
		else
		{	if ($("#blured-loading").is(':visible')) $("#blured-loading").hide ();
			$("#blured-text-message").text ((message?message:this.warn));
			$("#blured-progress").attr ("class", (localStorage.getItem ("login-response") === "success" ? this.progress.simple : this.progress.danger));
		}
	}
};
window["discover"] = function (entry)
{	var name = "", funky = "arguments.callee";

	while (eval (funky))
	if (eval (funky+".name").search (arguments[0]) == 0)
	{	name = eval (funky+".name");
		break;
	}
	else funky += ".caller";

	return name;
};