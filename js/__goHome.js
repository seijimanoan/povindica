var syncData =
{
	current_method: "get",
	current_object: "json",
	controller_path: "data/__goHome.php",
	__construct: function ()
	{	window.syncData.init_general().hookDOM ();
		if (window.sessionStorage.getItem("last-action").length == 0)
		window.syncData.init_general()["onStage"]({"method": "open"});
	},
	init_construct: function ()
	{	if ('localStorage' in window && window['localStorage'] !== null) window.sysAuth.logon ();
		else window.location = "http://www.google.com/intl/pt-BR/chrome/browser/";
	},
	init_general: function ()
	{	return {
			hookDOM: function ()
			{	$("[data-object]").off().on
				("click", function (e)
				{	e.preventDefault ();
					if ($(this).data ("method") && $(this).data ("job") === "enabled")
					{	syncData.init_general()[$(this).closest("[id^='on']").attr("id")](syncData.init_general().innerCall ($(this)));
						return true;
					}
					return false;
				});
			},
			innerCall: function (entry)
			{	var newer = new Array ();
				// Reading the Object and its Method
				newer["object"] = entry.data("method").toString().split("-").slice(0,1).toString ();
				newer["method"] = entry.data("method").toString().split("-").slice (1);
				// Modeling the Method name secondly its function as expected to be
				for (var n in newer["method"]) newer["method"][n] = n > 0 ? newer["method"][n].toString().charAt(0).toUpperCase()+newer["method"][n].slice(1) : newer["method"][n];
				newer["method"] = newer["method"].join ("");
				// We obtain our fucking parameters
				newer["params"] = entry.data("params").toString().split (",");
				for (var m in newer["params"]) newer["params"][newer["params"][m].toString().split(":")[0]] = newer["params"][m].toString().split(":")[1];
				// I <3 me
				newer["this"] = entry.data ("object");
				// Now we have the coordinates to run properly
				return newer;
			},
			onStage: function (thisArg)
			{	var persona = function (inherit, entry)
				{	return $.ajax
					({	type: window.syncData.current_method,
						url: window.syncData.controller_path,
						dataType: window.syncData.current_object,
						context: inherit,
						data:
						{	"go": window.syncData.current_object,
							"on": "getNameByCPF",
							"person": window.modelData.dom().cadastro.inputCPFNumber && window.modelData.dom().cadastro.inputCPFNumber.val().length == 11 ? window.modelData.dom().cadastro.inputCPFNumber.val () : ""
						},
						beforeSend: function ()
						{	window.trafficBox.show ();
						},
						success: function (e)
						{	if (e.callback.length == 0)
							{	window.doDebug.add ((e.response=="success"?1:0), e.response.toUpperCase()+" "+e.status, e.desc);
							}
							else
							{	window.modelData.dom().cadastro.inputCPFName.text (e.callback.pessoaCPF);
								window.modelData.dom().cadastro.inputCPFName.attr ("class", "label label-success");
								window.modelData.dom().cadastro.fieldset.start.prop ("disabled", true);
								window.modelData.dom().cadastro.fieldset.title.prop ("disabled", false);
							}
						},
						complete: function ()
						{	window.trafficBox.hide ();
							if (window.routeData.opt && window.location.hostname == "localhost") window.console.log (window.routeData.opt);
						}
					});
				};
				var captcha = function (inherit, entry)
				{	return $.ajax
					({	type: window.syncData.current_method,
						url: window.syncData.controller_path,
						dataType: window.syncData.current_object,
						context: inherit,
						data:
						{	"go": window.syncData.current_object,
							"on": "getImageOfCaptcha"
						},
						beforeSend: function ()
						{	window.trafficBox.show ();
						},
						success: function (e)
						{	if (e.callback.length == 0)
							{	window.doDebug.add ((e.response=="success"?1:0), e.response.toUpperCase()+" "+e.status, e.desc);
							}
							else
							{	window.modelData.dom().cadastro.outputCAPTCHAImage.attr ("src", e.callback.imageCaptcha);
								window.modelData.dom().cadastro.fieldset.start.prop ("disabled", false);
							}
						},
						complete: function ()
						{	window.trafficBox.hide ();
							if (window.routeData.opt && window.location.hostname == "localhost") window.console.log (window.routeData.opt);
						}
					});
				};
				var citizen = function (inherit, entry)
				{	return $.ajax
					({	type: window.syncData.current_method,
						url: window.syncData.controller_path,
						dataType: window.syncData.current_object,
						context: inherit,
						data:
						{	"go": window.syncData.current_object,
							"on": "getTrueCitizen",
							"born": window.modelData.dom().cadastro.inputBORNDate.val (),
							"mommy": window.modelData.dom().cadastro.inputMATERName.val (),
							"captcha": window.modelData.dom().cadastro.inputCAPTCHACode.val ()
						},
						beforeSend: function ()
						{	window.trafficBox.show ();
						},
						success: function (e)
						{	// alert (JSON.stringify (e));
							if (e.callback.length == 0)
							{	window.doDebug.add ((e.response=="success"?1:0), e.response.toUpperCase()+" "+e.status, e.desc);
								this.open ();
							}
							else
							{	window.modelData.dom().cadastro.outputCODETitle.text (e.callback["411f990cccf500604e3684b6989ed905"]);
								window.modelData.dom().cadastro.outputPLACETitle.text (e.callback["6833789c252b44ce3701e5146a9dbeb9"].localCidade+"-"+e.callback["6833789c252b44ce3701e5146a9dbeb9"].localEstado);
								window.modelData.dom().cadastro.fieldset.start.prop ("disabled", true);
								window.modelData.dom().cadastro.fieldset.title.prop ("disabled", true);
								window.modelData.dom().cadastro.fieldset.auth.prop ("disabled", false);
							}
						},
						complete: function (e)
						{	window.trafficBox.hide ();
							if (window.routeData.opt && window.location.hostname == "localhost") window.console.log (window.routeData.opt);
							// alert (JSON.stringify (e));
						}
					});
				};
				var join = function (inherit, entry)
				{	return $.ajax
					({	type: window.syncData.current_method,
						url: window.syncData.controller_path,
						dataType: window.syncData.current_object,
						context: inherit,
						data:
						{	"go": window.syncData.current_object,
							"on": "getNewUser",
							"username": window.modelData.dom().cadastro.inputUSERName && window.modelData.dom().cadastro.inputUSERName.val().length > 0 ? window.modelData.dom().cadastro.inputUSERName.val () : "",
							"password": window.modelData.dom().cadastro.inputPASSWord.val().length > 0 && window.modelData.dom().cadastro.inputPASSWord.val () == window.modelData.dom().cadastro.inputPASSMirror.val () ? window.modelData.dom().cadastro.inputPASSWord.val () : "",
							"email": window.modelData.dom().cadastro.inputMAILAddress && window.modelData.dom().cadastro.inputMAILAddress.val().length ? window.modelData.dom().cadastro.inputMAILAddress.val () : ""
						},
						beforeSend: function ()
						{	window.trafficBox.show ();
						},
						success: function (e)
						{	if (e.callback.length == 0)
							{	window.doDebug.add ((e.response=="success"?1:0), e.response.toUpperCase()+" "+e.status, e.desc);
								this.open ();
							}
							else
							{	window.doDebug.add ((e.response=="success"?1:0), e.response.toUpperCase()+" "+e.status, e.desc);
							}
						},
						complete: function (e)
						{	window.trafficBox.hide ();
							if (window.routeData.opt && window.location.hostname == "localhost") window.console.log (window.routeData.opt);
						}
					});
				};
				var system = {
					__construct: function (thisArg)
					{	if (typeof thisArg === "object" && typeof thisArg["method"] === "string" && thisArg["method"].length > 1)
						{	if (typeof this[thisArg["method"]] === "boolean") this[thisArg["method"]];
							else if (typeof this[thisArg["method"]] === "function") this[thisArg["method"]] (thisArg);
							else this["open"] (thisArg);

							return true;
						}
						return false;
					},
					open: function (thisArg)
					{	window.modelData.dom().cadastro.inputCPFNumber.mask ("99999999999");
						window.modelData.dom().cadastro.inputBORNDate.mask ("99/99/9999");
						captcha (this, thisArg);
						return true;
					},
					solicitaNome: function (thisArg)
					{	persona (this, thisArg);
						return true;
					},
					solicitaTitulo: function (thisArg)
					{	citizen (this, thisArg);
						return true;
					},
					solicitaCadastro: function (thisArg)
					{	join (this, thisArg);
						return true;
					}
				};
				return system.__construct (thisArg);
			}
		}
	}
};
var modelData =
{	dom: function ()
	{	return {
			cadastro:
			{	inputCPFNumber: $("#input_insert_cpf"),
				inputCPFName: $("#input_back_name"),
				fieldset:
				{	start: $("#level_0_form_register"),
					title: $("#level_1_form_register"),
					auth: $("#level_2_form_register")
				},
				inputBORNDate: $("#input_insert_borndate"),
				inputMATERName: $("#input_insert_matername"),
				inputCAPTCHACode: $("#input_insert_captchacode"),
				outputCAPTCHAImage: $("#input_insert_captchaimage"),
				outputCODETitle: $("#input_back_entitled"),
				outputPLACETitle: $("#input_back_address"),
				inputUSERName: $("#input_insert_username"),
				inputPASSWord: $("#input_insert_password"),
				inputPASSMirror: $("#input_insert_passredu"),
				inputMAILAddress: $("#input_insert_email")
			}
		}
	}
};