<?PHP
/*
$dominio = $_SERVER['HTTP_HOST'];
$resultado = explode("jobtime.com.ar",$dominio);

if($resultado[0] != "www")
{
    header( 'HTTP/1.1 301 Moved Permanently' );
    header( 'Location: http://www.jobtime.com.ar'.$resultado[1] );
    exit;
}
*/
 

if($_SERVER["REMOTE_ADDR"]=='201.235.161.66'){
	//error_reporting(E_ALL);
 error_reporting(0);
}
else
	error_reporting(0);

//Inicio de Session
session_start();


//INCLUDES VARIS
include("/kunden/homepages/22/d946514269/htdocs/includes/template.php");
include("/kunden/homepages/22/d946514269/htdocs/includes/config.php");
include("/kunden/homepages/22/d946514269/htdocs/includes/funciones.php");

if(isset($_GET['action']))
	$action = $_GET['action'];
else
  $action = "";


$t = new Template($templates , "remove");

/*
if ($_SERVER["REMOTE_ADDR"] != '201.235.161.66')
{
$t->set_file("pl", "mantenimiento.htm");
$t->parse("MAIN", "pl");
$t->p("MAIN");
exit;
}*/

// mantenimiento
/*
$t->set_file("pl", "mantenimiento.htm");
$t->parse("MAIN", "pl");
$t->p("MAIN");
exit;
*/
/*mysql_pconnect($mysql_host, $mysql_username, $mysql_passwd) or die ("No consegu� conectar!");
mysql_select_db($mysql_database) or die ("No consegu� conectar con la base de datos!");*/

 $mysql_host = "db5011399537.hosting-data.io";
 $mysql_username = "dbu4047347";
 $mysql_passwd = "EmmaColella2018";
 $mysql_database = "dbs9617828";
 
$db = new mysqli($mysql_host, $mysql_username, $mysql_passwd, $mysql_database);

// Check connection
if (!$db) {
    die("Connection failed: " . mysqli_connect_error());
}




if ($action == "" || $action == "mostrar_home_page")  
{
	if(isset($_GET['ultimos']))
		{
			if($_GET['ultimos'] == 1)
				mostrar_home_page_ultimos();
			else
			{
				$tn = new Template($templates_new , "remove");
				$t = $tn;
				mostrar_home_new();
				//mostrar_home_page();
			}

		}
	else
	{
		$tn = new Template($templates_new , "remove");
		$t = $tn;
		mostrar_home_new();
		//mostrar_home_page();
	}

}
elseif($action == "home")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	mostrar_home_new();
	
}	
elseif($action == "logout")
{
	logout();
}
elseif($action == "enviar_amigo")
{
	enviar_amigo();
}
elseif($action == "newsletter")
{
	newsletter();
}
elseif($action == "recuperar")
{
	recuperar_pass();
}
elseif($action == "paginas_amigas")
{
	paginas_amigas();
}
elseif($action == "ver_cv")
{
	//ver_cv();
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	ver_cv_new();
}
elseif($action == "enviar_pass")
{
	enviar_pass();
}
elseif($action == "modif_contra")
{
	modif_contra();
}
//elseif($action == "nueva_empresa")
//{
	//nueva_empresa();
//}
elseif($action == "alta_empresa")
{
	//alta_empresa();
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	alta_empresa_new();
}
elseif($action == "validarEmpres")
{
	validarEmpres();
}
elseif($action == "enviar_empresa")
{
	enviar_empresa();
}
elseif($action == "enviar_empresa_captcha")
{
	enviar_empresa_captcha();
}
elseif($action == "ver_empresa")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	$id_empresa = $_GET["id_empresa"];
	ver_empresa($id_empresa,$t);
}
elseif($action == "imprimircv")
{
	generar_pdf();
}
elseif($action == "ingresar_cv")
{
	//ingresarCv();
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	ingresarCv_new();
}
elseif($action == "registrarce")
{
	registrarce();
}
elseif($action == "contacto")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	contacto();
}
elseif($action == "terminios")
{
	terminios();
}
elseif($action == "express")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	aviso_express();
}
elseif($action == "registro_paso1")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	registro_paso1();
}
elseif($action == "editar_esutio")
{
	//registro_paso2();
	editar_esutio($_GET['id_estudio']);
}
elseif($action == "registro_paso2")
{
	//registro_paso2();
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	registro_paso_dos($t);
}
elseif($action == "ver_paso3")
{
	if(isset($_SESSION['usuario']))
		$u = $_SESSION['usuario'];
	else
		$u = "";
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	ver_paso3($u,$t);
}
elseif($action== "nueva_experiencia")
{
	if(isset($_SESSION['usuario']))
		nueva_experiencia();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "add_face")
{
	if(isset($_SESSION['usuario']))
		add_face();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "add_twit")
{
	if(isset($_SESSION['usuario']))
		add_twit();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "borrar_twit")
{
	if(isset($_SESSION['usuario']))
		borrar_twit();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "adjuntar_cv")
{
	if(isset($_SESSION['usuario']))
		adjuntar_cv();
	else
		echo "No tiene acceso a esta area";
}
elseif($action== "borrar_cv")
{
	if(isset($_SESSION['usuario']))
		borrar_cv();
	else
		echo "No tiene acceso a esta area";
}
elseif($action== "borrar_face")
{
	if(isset($_SESSION['usuario']))
		borrar_face();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "agregar_nueva_experiencia")
{
	if(isset($_SESSION['usuario']))
		agregar_nueva_experiencia();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "guardar_nuevo_estudio")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;

	if(isset($_SESSION['usuario']))
		guardar_nuevo_estudio($db,$t);
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "actualizar_congreso")
{
	if(isset($_SESSION['usuario']))
		actualizar_congreso();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "ver_nuevo_estudio")
{
	if(isset($_SESSION['usuario']))
		ver_nuevo_estudio();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "borrar_estudio")
{
	if(isset($_SESSION['usuario']))
		borrar_estudio();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "borrar_idioma")
{
	if(isset($_SESSION['usuario']))
		borrar_idioma();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "borrar_curso")
{
	if(isset($_SESSION['usuario']))
		borrar_curso();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "update_curso")
{
	if(isset($_SESSION['usuario']))
		update_curso();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "ver_nuevo_congreso")
{
	if(isset($_SESSION['usuario']))
		ver_nuevo_congreso();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "guardar_nuevo_congreso")
{
	if(isset($_SESSION['usuario']))
		guardar_nuevo_congreso();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "borrar_congreso")
{
	if(isset($_SESSION['usuario']))
		borrar_congreso();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "borrar_experiencia")
{
	if(isset($_SESSION['usuario']))
		borrar_experiencia();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "actualizar_experiencia")
{
	if(isset($_SESSION['usuario']))
		actualizar_experiencia($db);
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "update_idiomas")
{
	if(isset($_SESSION['usuario']))
		update_idiomas();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "ver_nuevo_curso")
{
	if(isset($_SESSION['usuario']))
		ver_nuevo_curso();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "ver_nuevo_idioma")
{
	if(isset($_SESSION['usuario']))
		ver_nuevo_idioma();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "guardar_nuevo_idiomas")
{
	if(isset($_SESSION['usuario']))
		guardar_nuevo_idioma();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "guardar_nuevo_curso")
{
	if(isset($_SESSION['usuario']))
		guardar_nuevo_curso();
	else
		echo "No tiene acceso a esta area";	
}
elseif($action== "update_estudio")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;

	if(isset($_SESSION['usuario']))
		actualizar_estudio($t);
	else
		echo "No tiene acceso a esta area";	
}
elseif($action == "registro_paso3")
{
	registro_paso3();
}
elseif($action == "editar_curso")
{
	if(isset($_SESSION['usuario']))
		editar_curso($_SESSION['usuario'],$_GET['id_curso']);
	else
		echo "No tiene acceso a esta area!";
	
}
elseif($action == "editar_idioma")
{
	editar_idioma($_SESSION['usuario'],$_GET["id_idioma"]);
}
elseif($action == "editar_congresos")
{
	editar_congresos($_SESSION['usuario'],$_GET["id_congreso"]);
}

elseif($action == "quienes_somos")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	quienes_somos($t);
}
elseif($action == "servicios")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	servicios();
}
elseif($action == "clientes")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	clientes();
}
elseif($action == "logearce")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	logearce();
}
elseif($action == "entrar")
{
	entrar();
}
elseif($action == "registrar")
{
	registrarce();
}
elseif($action == "editarCv")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	editarCv();
}
elseif($action == "guardar_paso1_salir")
{
	guardar_paso1_salir();
}
elseif($action == "guardar_paso_3")
{
	guardar_paso_3();
}
elseif($action == "agregar_estudio")
{
	agregar_estudio();
}
elseif($action == "cambiar_imagen")
{
	cambiar_imagen();
}
elseif($action == "enviar_contacto")
{
	enviar_contacto();
}
elseif($action == "empleos")
{
	//ver_empleos();
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	ver_empleos_new();
}

elseif($action == "postular")
{
	postular();
}
/*elseif($action == "detalle")
{
	detalle();
}*/
elseif($action == "detalle")
{
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	detalle_new();
}
elseif($action == "enviar_postulacion")
{
	enviar_postulacion();
}
elseif($action == "buscar_empleo")
{
	//buscar_empleo();
	$tn = new Template($templates_new , "remove");
	$t = $tn;
	buscar_empleo_new();

}
elseif($action == "mapa")
{
	mapaweb();
}
if(isset($_SESSION['usuario']))
	{
	//ActivarSessionCokie();	
	$t->set_var("mostrar", "_visible");
	$t->set_var("ocultar", "_oculto");
	$user = getUsuario($_SESSION['usuario']);
	getCantPostulacionesUsuario($_SESSION['usuario'], $datos);
	$t->set_var("posul_vigent",$datos['activos']);
	$t->set_var("posul_total",$datos['total']);	
	$t->set_var("posul_caduc",$datos['caducados']);
	$t->set_var("user", $user);
	$t->set_var("dni_usuario", $_SESSION['usuario']);
	$t->set_var("ocultar_munieco", "style='display:none;'");
		$t->set_var("url_logeado", "/?action=logearce");
		$t->set_var("ocultar_user_loguiado", "style='display:none;'");
	$t->set_var("mostrar_user_loguiado", "style='display:block !important;'");
	$t->set_var("inicio_login", '<span class="redonda_user">'.strtoupper($user[0]).'</span> Mi cuenta');
	
	}
else
{
	$t->set_var("url_logeado", "/ingresar_cv#tab2");
	$t->set_var("ocultar_datos", "_oculto");
$t->set_var("inicio_login", 'Ingresar');
}

if($action != "detalle")		
{
	if($action == "") $action = "home";
	$t->set_var("gtm","<script type='text/javascript'>

var _gaq = _gaq || [];
_gaq.push(['_setAccount', 'UA-17421648-1']);
_gaq.push(['_trackPageview' ,'/".$action.".htm']);

(function() {
	var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
	ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
	var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
})();

</script>");
}

setear_cantidades();

$t->parse("MAIN", "pl");
$t->p("MAIN");




?>
