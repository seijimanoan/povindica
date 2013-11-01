<!DOCTYPE html>
<html>
<!--
Copyright 2013 de SEIJI MANOAN SEO
Este arquivo é parte do programa POVINDICA BRASIL. O POVINDICA BRASIL é um software livre; você pode redistribuí-lo e/ou modificá-lo dentro dos termos da [GNU General Public License OU GNU Affero General Public License] como publicada pela Fundação do Software Livre (FSF); na versão 3 da Licença. Este programa é distribuído na esperança que possa ser útil, mas SEM NENHUMA GARANTIA; sem uma garantia implícita de ADEQUAÇÃO a qualquer MERCADO ou APLICAÇÃO EM PARTICULAR. Veja a licença para maiores detalhes. Você deve ter recebido uma cópia da [GNU General Public License OU GNU Affero General Public License], sob o título "LICENCA.txt", junto com este programa, se não, acesse http://www.gnu.org/licenses/
-->
<head>
	<title>Povindica Brasil</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0, minimum-scale=1.0, maximum-scale=1.0, user-scalable=no">
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	<link href="./css/bootstrap.min.css" rel="stylesheet" media="screen">
    <link href="./css/bootstrap-glyphicons.css" rel="stylesheet" media="screen">
	<link href rel="stylesheet" media="screen" id="cssView">
	<link id="jsView">
	
	<style>
/*
//	Default
*/
@font-face
{	font-family: 'roboto'; font-style: normal; font-weight: 400; src: local('roboto regular'), local('roboto-regular'), url(fonts/roboto-regular.woff) format('woff');
}
@font-face
{	font-family: 'roboto'; font-style: normal; font-weight: 300; src: local('roboto light'), local('roboto-light'), url(fonts/roboto-light.woff) format('woff');
}
html, div, section, article
{	display: block; margin: 0; padding: 0;
}
body
{	padding: 70px 0 0 0; margin: 0;
	background-color: #fff;
	display: block;
	font-family: "roboto", arial, sans-serif; font-weight: 400;
}
h1, h2, h3, h4, h5, h6
{	font-style: normal; font-weight: 100; line-height: 1.15em; margin: 0 0 .3em 0; font-family: "roboto", arial, sans-serif; font-weight: 300;
}
.modal
{	z-index: 3500;
}
.barspace
{	height: 50px;
}
.tab-content .barspace
{	height: 15px;
}
/*
//	Our dear loading box
*/
#blured-loading
{	background: -moz-radial-gradient(center, ellipse cover,  rgba(0,0,0,0) 0%, rgba(0,0,0,0.65) 100%);
	background: -webkit-gradient(radial, center center, 0px, center center, 100%, color-stop(0%,rgba(0,0,0,0)), color-stop(100%,rgba(0,0,0,0.65)));
	background: -webkit-radial-gradient(center, ellipse cover,  rgba(0,0,0,0) 0%,rgba(0,0,0,0.65) 100%);
	background: -o-radial-gradient(center, ellipse cover,  rgba(0,0,0,0) 0%,rgba(0,0,0,0.65) 100%);
	background: -ms-radial-gradient(center, ellipse cover,  rgba(0,0,0,0) 0%,rgba(0,0,0,0.65) 100%);
	background: radial-gradient(ellipse at center,  rgba(0,0,0,0) 0%,rgba(0,0,0,0.65) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#00000000', endColorstr='#a6000000',GradientType=1 );
	position: fixed; top: 50px; bottom: 0; right: 0; left: 0; display: none; z-index: 3400;
	margin: 0 auto; padding: 0 auto;
}
.blured-box
{	position: relative; margin: 70px auto; background: rgb(20,20,20); width: 400px;
	background: -moz-linear-gradient(top,  rgba(20,20,20,1) 0%, rgba(50,52,56,1) 100%);
	background: -webkit-gradient(linear, left top, left bottom, color-stop(0%,rgba(20,20,20,1)), color-stop(100%,rgba(50,52,56,1)));
	background: -webkit-linear-gradient(top,  rgba(20,20,20,1) 0%,rgba(50,52,56,1) 100%);
	background: -o-linear-gradient(top,  rgba(20,20,20,1) 0%,rgba(50,52,56,1) 100%);
	background: -ms-linear-gradient(top,  rgba(20,20,20,1) 0%,rgba(50,52,56,1) 100%);
	background: linear-gradient(to bottom,  rgba(20,20,20,1) 0%,rgba(50,52,56,1) 100%);
	filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#141414', endColorstr='#323438',GradientType=0 );
	-moz-box-shadow: rgba(0, 0, 0, 0.498039) 0px 5px 15px 0px; -webkit-box-shadow: rgba(0, 0, 0, 0.498039) 0px 5px 15px 0px; box-shadow: rgba(0, 0, 0, 0.498039) 0px 5px 15px 0px;
	border: 1px rgba(0, 0, 0, 0.2) inset; -webkit-border-radius: 6px; -moz-border-radius: 6px; border-radius: 6px;
}
.blured-title
{	display: block; border-bottom: solid 1px rgba(255,255,255,0.15); padding: 10px 15px; color: #CCC; font-family: "roboto", arial, sans-serif; font-weight: 300;
}
.blured-content
{	padding: 25px 15px; color: white; font-family: "roboto", arial, sans-serif; font-weight: 300;
}
/*
//	The Demeter's about window modal design
*/
.about_on
{	background: url(img/demeter.png) center center no-repeat; height: 387px;
}
.about_on:hover > .about_off
{	background: rgba(255,255,255,.8); visibility: visible; min-height: 387px;
}
.about_off
{	visibility: hidden;
}
	</style>
</head>
<body style="zoom: 1;">
	<nav class="navbar navbar-inverse navbar-fixed-top" role="navigation">
		<div class="navbar-header">
			<button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-ex1-collapse"><span class="sr-only">Toggle navigation</span><span class="icon-bar"></span><span class="icon-bar"></span><span class="icon-bar"></span></button>
			<a class="navbar-brand visible-lg" href="#demAbout" data-toggle="modal">Povindica Brasil</a>
		</div>
		<div class="collapse navbar-collapse navbar-ex1-collapse">
			<ul id="user-menu-bar" class="nav navbar-nav"><li><a href="#goHome">Principal</a></li></ul>
			<ul id="etc_long" class="nav navbar-nav"></ul>
			<ul class="nav navbar-nav navbar-right">
				<li><a href="#system_show_debug" class="visible-lg" data-toggle="modal"><span class="glyphicon glyphicon-info-sign"></span> Debug</a></li>
				<li><a href="http://validator.w3.org/check?uri=https%3A%2F%2Ffederalst.com.br%2Fatendimento%2Fdemeter%2F%23%21%2FgoHome&charset=%28detect+automatically%29&doctype=Inline&group=0" class="visible-lg" target="_blank"><span class="glyphicon glyphicon-check"></span> Check</a></li>
				<li><a href="#mod_prest_details" data-toggle="modal"><span class="visible-lg"><span class="glyphicon glyphicon-user"></span> User</span><span class="hidden-lg"><span class="glyphicon glyphicon-user"></span></span></a></li>
			</ul>
		</div>
	</nav>
	<section id="htmlView"></section>

<div id="demAbout" class="modal" tabindex="-1" role="dialog" aria-labelledby="modal_for_about_us" aria-hidden="false">
<div class="modal-dialog">
<div class="modal-content">
	<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
	<h3 id="modal_for_about_us">Povindica Brasil</h3>
	</div>
	<div class="modal-body">
		<div class="about_on"><div class="about_off"><p style="text-align: justify;">Propriedade intelectual desenvolvida sob os dom&iacute;nios da FEDERAL ST &copy; para fins exclusivos. Todos os direitos reservados. Algumas imagens utilizadas podem conter algum direito autoral respectivo.</p><ul><li>Desenvolvido com o framework jQuery (http://jquery.com/) para JavaScript.</li><li>Desenvolvido com o framework Bootstrap (http://twitter.github.com/bootstrap/) para folha de estilos.</li><li>Desenvolvido com a linguagem PHP (http://www.php.net/) para programa&ccedil;&atilde;o.</li></ul><p>Desenvolvimento por <strong>Seiji Manoan Seo</strong> (PHP Analyst Programmer) sob a gestão de Marcelo Augusto Pedreira Xavier (Information Technology Manager).</p></div></div>
	</div>
	<div class="modal-footer">
		<button class="btn btn-default" data-dismiss="modal" aria-hidden="true">Fechar</button>
		<a class="btn btn-primary" href="mailto:suporte@federalst.com.br?Subject=Demeter">Contatar administrador</a>
	</div>
</div>
</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="system_show_debug" class="modal fade in" tabindex="-1" role="dialog" aria-labelledby="modal_for_debug" aria-hidden="true"><div class="modal-dialog"><div class="modal-content">
	<div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h3 id="modal_for_debug">Debug</h3></div>
	<div class="modal-body"><div id="debug_outputs_in_here"></div></div>
	<div class="modal-footer"><button class="btn btn-info" data-dismiss="modal" aria-hidden="true">Fechar</button></div>
</div></div></div>
<div id="mod_prest_details" class="modal fade in" tabindex="-1" role="dialog" aria-hidden="false">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
			  <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button><h4 class="modal-title">Autentica&ccedil;&atilde;o de usu&aacute;rio</h4>
			</div>
			<div class="modal-body"><div id="login_alert"></div>
				<form class="form-horizontal">
					<div class="form-group"><label  for="login_user" class="col-lg-2 control-label">Usu&aacute;rio</label><div class="col-lg-10"><input type="text" class="form-control" id="login_user" placeholder="Usuário"></div></div>
					<div class="form-group"><label for="login_pass" class="col-lg-2 control-label">Senha</label><div class="col-lg-10"><input type="password" class="form-control" id="login_pass" placeholder="Password"><input type="hidden" id="login_data"></div></div>
				</form>
			</div>
			<div class="modal-footer">
				<button id="login_exit" class="btn btn-danger pull-left" data-dismiss="modal" aria-hidden="true"><i class="icon-lock icon-white"></i> Log Out</button><button class="btn btn-link">Limpar</button><button id="login_entrance" class="btn btn-primary"><i class="icon-user icon-white"></i> Log In</button>
			</div>
		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<div id="blured-loading">
	<div class="blured-box">
		<div class="blured-title">We are up in communication</div>
		<div class="blured-content">
			<div class="progress progress-striped active"><div id="blured-progress" class="progress-bar" style="width: 100%;"></div></div><p id="blured-text-message">Carregando, por favor aguarde...</p>
		</div>
	</div><!-- /.modal-dialog -->
</div><!-- /.modal -->
<script src="./js/jquery.min.js"></script>
<script src="./js/system.js"></script>
<script src="./js/jmasky.min.js"></script>
<script src="./js/bootstrap.min.js"></script>
<script>
sessionStorage.setItem ("last-module", "");
$(document).ready (function () { routeData.remote (); });
</script>
</body>
</html>