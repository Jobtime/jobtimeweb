<?
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
 

//Inicio de Session
session_start();


//INCLUDES VARIS
include("includes/template.php");
include("includes/config.php");
include("includes/funciones.php");

$action = $_GET['action'];

$t = new Template($templates , "remove");
/*
if ($_SERVER["REMOTE_ADDR"] != '212.169.208.203')
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

$db = mysqli_connect($mysql_host, $mysql_username, $mysql_passwd, $mysql_database);
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
				mostrar_home_page();	
		}
	else
		mostrar_home_page();
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
	ver_cv();
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
	alta_empresa();
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
elseif($action == "imprimircv")
{
	generar_pdf();
}
elseif($action == "ingresarCv")
{
	ingresarCv();
}
elseif($action == "registrarce")
{
	registrarce();
}
elseif($action == "contacto")
{
	contacto();
}
elseif($action == "terminios")
{
	terminios();
}
elseif($action == "express")
{
	aviso_express();
}
elseif($action == "registro_paso1")
{
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
	registro_paso_dos();
}
elseif($action == "ver_paso3")
{
	if(isset($_SESSION['usuario']))
		$u = $_SESSION['usuario'];
	else
		$u = "";	
	ver_paso3($u);
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
	if(isset($_SESSION['usuario']))
		guardar_nuevo_estudio();
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
		actualizar_experiencia();
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
	if(isset($_SESSION['usuario']))
		actualizar_estudio();
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
		editar_curso($_SESSION['usuario']);
	else
		echo "No tiene acceso a esta area!";
	
}
elseif($action == "editar_idioma")
{
	editar_idioma($_SESSION['usuario']);
}
elseif($action == "editar_congresos")
{
	editar_congresos($_SESSION['usuario']);
}

elseif($action == "quienes_somos")
{
	quienes_somos();
}
elseif($action == "Servicios")
{
	servicios();
}
elseif($action == "clientes")
{
	clientes();
}
elseif($action == "logearce")
{
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
	ver_empleos();
}
elseif($action == "postular")
{
	postular();
}
elseif($action == "detalle")
{
	detalle();
}
elseif($action == "enviar_postulacion")
{
	enviar_postulacion();
}
elseif($action == "buscar_empleo")
{
	buscar_empleo();
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

	
	}
else
$t->set_var("ocultar_datos", "_oculto");

setear_cantidades();

$t->parse("MAIN", "pl");
$t->p("MAIN");




?>
