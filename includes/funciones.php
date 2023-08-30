<?php

require_once("funciones_globales.php");

// *****************************************************************************************************
// Inicio de Funciones Principales
// *****************************************************************************************************


function actualizar_estudio($t)
{
global $db;	
	
$estudio = $_POST['Festudio'];
$titulo = $_POST['Ftitulo'];
$estado = $_POST['Festado'];
$ciudad = $_POST['location1'];
$Fingreso = $_POST['Fingreso'];
$Fegreso = $_POST['Fegreso'];
$Finstitu = $_POST['Finsti'];
$Finstitucion = $_POST['otra_istitucion'];
$Nestudio = $_POST['numero_estudio'];

$sql = "UPDATE estudios SET estudio = $estudio, institucion = '$Finstitu', titulo = '$titulo', estado = '$estado', anioIngreso = $Fingreso, anioEgreso = $Fegreso, otra_istitucion = '$Finstitucion' WHERE id_estudio = ".$Nestudio;

$result=mysqli_query($db,$sql);

$myusername = $_SESSION['usuario'];

ver_paso_dos($myusername,$t);

}

function getRealIP()
{

    if (isset($_SERVER["HTTP_CLIENT_IP"]))
    {
        return $_SERVER["HTTP_CLIENT_IP"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED_FOR"]))
    {
        return $_SERVER["HTTP_X_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_X_FORWARDED"]))
    {
        return $_SERVER["HTTP_X_FORWARDED"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED_FOR"]))
    {
        return $_SERVER["HTTP_FORWARDED_FOR"];
    }
    elseif (isset($_SERVER["HTTP_FORWARDED"]))
    {
        return $_SERVER["HTTP_FORWARDED"];
    }
    else
    {
        return $_SERVER["REMOTE_ADDR"];
    }

}

function getCantPostulacionesUsuario($user, &$datos)
{
	global $db,$t;	
	
	$sql = "select count(e.activo) as cantidad_desactivos from postulaciones p, empleos e where p.usuario = ".$user." and p.id_empleo = e.id_empleo and e.activo = 0";

	$result=mysqli_query($db,$sql);
	
	$sql2 = "select count(e.activo) as cantidad_activos from postulaciones p, empleos e where p.usuario = ".$user." and p.id_empleo = e.id_empleo and e.activo = 1";
	
	$result2=mysqli_query($db,$sql2);

	$resultado = mysqli_fetch_array($result);
	
	$resultado2 = mysqli_fetch_array($result2);
	
	$datos['caducados'] = $resultado['cantidad_desactivos'];
	$datos['activos'] = $resultado2['cantidad_activos'];
	$datos['total'] = $datos['caducados'] + $datos['activos'];
}

function setear_cantidades()
{
global $db,$t;	
	
$sql = "select count(*) as cantidad from usuario";
$sql2 = "select count(*) as cantidad_empleos from empleos";
$sql3 = "select count(*) as cantidad_empresas from empresas";

$result=mysqli_query($db,$sql);
$result2=mysqli_query($db,$sql2);
$result3=mysqli_query($db,$sql3);

$arreglo = mysqli_fetch_array($result);
$arreglo2 = mysqli_fetch_array($result2);
$arreglo3 = mysqli_fetch_array($result3);


$t->set_var("cantidad_user",$arreglo['cantidad']);
$t->set_var("empleos_vigentes",$arreglo2['cantidad_empleos']);
$t->set_var("empresas_vigentes",$arreglo3['cantidad_empresas']);
	
}

function reemplazar_url($string, &$result)
{
$string = ereg_replace("[�����]","a",$string);
$string = ereg_replace("[�����]","A",$string);
$string = ereg_replace("[����]","I",$string);
$string = ereg_replace("[����]","i",$string);
$string = ereg_replace("[����]","e",$string);
$string = ereg_replace("[����]","E",$string);
$string = ereg_replace("[�����]","o",$string);
$string = ereg_replace("[�����]","O",$string);
$string = ereg_replace("[����]","u",$string);
$string = ereg_replace("[����]","U",$string);
$string = str_replace("�","c",$string);
$string = str_replace("�","C",$string);
$string = str_replace("�","n",$string);
$string = str_replace(" ","-",$string);
$string = str_replace("�","N",$string);
$result = strtolower($string);
}



function newsletter()
{
global $db,$t;

$mail = $_POST['mail_news'];

$sqlc = "select * from newsletter where mail = '".$mail."'";

$result=mysqli_query($db,$sqlc);

$number_of_products = mysqli_numrows($result);

$fecha_actual  = date("Y-m-d");

if($number_of_products == 0)
{
$sql = "insert into newsletter (mail, fecha) values ('".$mail."', '".$fecha_actual."')";

mysqli_query($db,$sql);

$para = $mail;

$titulo = 'Suscripción al Newsletter de JobTime';

// message
$mensaje = '
<table width="560" border="0" cellspacing="0" cellpadding="0" >
  <tr>
    <td width="30" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_izq.jpg" /></td>
    <td width="490"><img src="http://www.jobtime.com.ar/images/top_mail.gif" /></td>
    <td width="26" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_der.jpg" /></td>
  </tr>
  <tr>
    <td>
    	<p style="padding:10px;">Bienvenidos al newsletter de Jobtime Empleos. En forma periódica podrás tener en tu email un resumen de los nuevos empleos subidos a nuestra pagina, las nuevas empresas que se incorporan a jobtime y noticias laborales de todo tipo, tendencias, capacitaciones y mucho mas!
Te damos una grata bienvenida a nuestro Newsletter y te deseamos mucha suerte!!!

</p>
     
    <p style="padding:10px; width=100%; text-align:right;">El Equipo de <strong><span style="color:#81a60f">JOB</span><span>TIME</span>!</strong></p>
    </td>
  </tr>
</table>
';


    //mando el correo...

	$from = "newsletter@jobtime.com.ar";
	
	if ( strpos($para, ".ru") === false)
		mail_php($para,$mensaje,"Jobtime - Suscripción al Newsletter");
		//enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Suscripción al Newsletter",$mensaje);
}

mostrar_home_page();

}

function mostrar_home_page_ultimos()
{
	global $db,$t;
	
	$t->set_file("pl", "index.htm");
	cargar_areas("");
	cargar_provincias("");
	
	cargar_ofertas_home_ultimos("","");
	cargar_ofertas_home_express("","");
	
	
	$t->set_var("empleo_potulado","/empleo-");
	
	parsear_logos_empresas();
	
	if(isset($_GET['error']))
		{
			$t->set_var("mostrar_error", "_mostrar");	
		}
	
}

function mostrar_home_page()
{
	global $db,$t;
	
	$t->set_file("pl", "index.htm");
	cargar_areas("");
	cargar_provincias("");
	
	cargar_ofertas_home("","");
	cargar_ofertas_home_express("","");
	
	$t->set_var("empleo_potulado","/empleo-");
	
	parsear_logos_empresas();
	
	if(isset($_GET['error']))
		{
			$t->set_var("mostrar_error", "_mostrar");	
		}
	
}

function mostrar_home_new()
{
	global $db,$tn;
	
	$tn->set_file("pl", "index.html");
	//cargar_areas_new("",$tn);
	//cargar_provincias_new("",$tn);
	
	cargar_ofertas_home_express_new(0);
	cargar_ofertas_home_new("","");
	
	
	$tn->set_var("empleo_potulado","/empleo-");
	
	//parsear_logos_empresas_new($tn);
	
	if(isset($_GET['error']))
		{
			$tn->set_var("mostrar_error", "_mostrar");	
		}
	
}

function paginas_amigas()
{
	global $db,$t;

$t->set_file("pl", "paginas-amigas.htm");

$t->set_block("pl", "paginas_amigas","_paginas_amigas");
$t->set_block("pl", "empresas","_empresas");

$sqle = "select * from empresas";

$resulte = mysqli_query($db,$sqle);

while($paginase = mysqli_fetch_array($resulte))
{
	
	
	$t->set_var("nombre_empresa",$paginase['logo']);
	$t->parse("_empresas", "empresas",true);
	
}


$sql = "select * from paginas_amigas";

$result = mysqli_query($db,$sql);

while($paginas = mysqli_fetch_array($result))
{
	
	
	$t->set_var("nombre",$paginas['nombre']);
	$t->set_var("url_amiga",$paginas['url']);
	$t->parse("_paginas_amigas", "paginas_amigas",true);
	
}


}

function ver_cv()
{
global $db,$t;

//$t = new Template($template_cv , "remove");

if($_GET['usuario'] != $_SESSION['usuario'])
$t->set_file("pl", "error.htm");
else {
	if (strpos($_SERVER["HTTP_HOST"], "localhost") === false)
		$template_cv = "/storage/content/72/1009672/jobtime.com.ar/public_html/php_templates/cv/";
	else
		$template_cv = "/Users/rodrigocolella/Sites/jobtime-online/php_templates/cv/";

	$t->set_file("pl", $template_cv . "cv.htm");
//$t->set_file("pl", "cv.htm");
	$t->set_block("pl", "estudios", "_estudios");
	$t->set_block("pl", "experiencia", "_experiencia");


	$usuario = str_replace(".", "", $_GET['usuario']);

	if (!is_numeric($usuario)) {
		echo "No es un usuario REAL!";
		exit;
	} else {
		$sql = "select * from usuario where dni = " . $usuario . " ORDER BY apellido";

		$sql_estudios = "select * from estudios where dni = " . $usuario;

//$sql_laboral = "select * from laboral where dni = ".$usuario;

		$sql_laboral = "SELECT * FROM laboral, jerarquias, ramos  WHERE laboral.dni = " . $usuario . " AND laboral.actividad = jerarquias.id_jerarquia AND ramos.numero_ramo = laboral.ramo";

		$resul_laboral = mysqli_query($db, $sql_laboral);

		$resul_estudio = mysqli_query($db, $sql_estudios);

		$contadorEstudios = mysqli_num_rows($resul_estudio);

		$contadorLaboral = mysqli_num_rows($resul_laboral);

		$result = mysqli_query($db, $sql);

		$number_of_products = mysqli_num_rows($result);

//seteo los datos del usuario
		$datos_usuario = mysqli_fetch_array($result);

		$t->set_var("apellido", $datos_usuario['apellido']);
		$t->set_var("nombre", $datos_usuario['nombre']);
		$t->set_var("dni", $datos_usuario['dni']);
		$t->set_var("mail", $datos_usuario['email']);

		if ($datos_usuario['facebook'] != '') {
			$t->set_var("mostrar_face", "block;");
			$t->set_var("link_facebook", $datos_usuario['facebook']);
		} else
			$t->set_var("mostrar_face", "none;");

		if ($datos_usuario['twitter'] != '') {
			$t->set_var("mostrar_linkedin", "block;");
			$t->set_var("link_linkedin", $datos_usuario['twitter']);
		} else
			$t->set_var("mostrar_linkedin", "none;");

		$t->set_var("telefono", $datos_usuario['telefonoContacto']);
		$civil = $datos_usuario["estadoCivil"];
		if ($civil == 1)
			$estado_civil = "Soltero/a";
		elseif ($civil == 2)
			$estado_civil = "Casado/a";
		elseif ($civil == 3)
			$estado_civil = "Concubinato/a";
		elseif ($civil == 4)
			$estado_civil = "Viudo/a";
		$t->set_var("civil", $estado_civil);
		if ($datos_usuario["remuneracion"] == '0')
			$sueldo = "No espeficicó";
		else
			$sueldo = $datos_usuario["remuneracion"] . " Pesos";
		$t->set_var("remuneracion", $sueldo);
		$t->set_var("hijos", $datos_usuario["hijos"]);
		$t->set_var("domicilio", $datos_usuario["domicilio"]);
		$nombreProv = getProvincia($datos_usuario["provincia"]);
		$t->set_var("provincia", $nombreProv);
		$nombreCiu = getCiudad($datos_usuario["ciudad"]);
		$t->set_var("ciudad", $nombreCiu);
		$nombrePai = getPais($datos_usuario["pais"]);
		$t->set_var("pais", $nombrePai);
		$t->set_var("fecha_nacimiento", $datos_usuario["fechaNacimiento"]);

		$idiomasJuntos = "";
		$idiomatmp = "";
		$congresosJuntos = "";
//estudios
		while ($estudios = mysqli_fetch_array($resul_estudio)) {

			$estudio = getEstudio($estudios["estudio"]);
			$t->set_var("estudio", $estudio);
			$t->set_var("titulo", $estudios["titulo"]);
			$estado = getEstado($estudios["estado"]);
			$t->set_var("estado", $estado);
			$inst = getInstitucion($estudios["institucion"]);
			$t->set_var("institucion", $inst);

			$t->parse("_estudios", "estudios", true);
		}

		$sql_idiomas = "SELECT * FROM idiomas WHERE dni = " . $usuario;
		$resul_idiomas = mysqli_query($db, $sql_idiomas);

		$idiom = "";
		while ($idiomas = mysqli_fetch_array($resul_idiomas))
			$idiom = $idiom . $idiomas['nombre'] . ", Nivel: " . $idiomas['nivel'] . " | ";

		$t->set_var("idiomas", $idiom);

		$sql_cursos = "SELECT * FROM cursos WHERE dni = " . $usuario;
		$resul_cursos = mysqli_query($db, $sql_cursos);

		$cur = "";
		while ($cursos = mysqli_fetch_array($resul_cursos))
			$cur = $cur . $cursos['curso'] . " <br /><br /> ";

		$t->set_var("cursos", $cur);

		$sql_congresos = "SELECT * FROM congresos WHERE dni = " . $usuario;
		$resul_congresos = mysqli_query($db, $sql_congresos);

		$congre = "";
		while ($congresos = mysqli_fetch_array($resul_congresos))
			if ($congresos['congreso'] != "")
				$congre = $congre . $congresos['congreso'] . " <br /><br /> ";

		if ($congre == "")
			$congre = "No especific&oacute;";

		$t->set_var("congresos", $congre);


// experiencias
		while ($laboral = mysqli_fetch_array($resul_laboral)) {
			if ($laboral['empresa'] != "") {
				$t->set_var("empresa", $laboral['empresa']);
				$t->set_var("desde", $laboral['desde']);
				$t->set_var("hasta", $laboral['hasta']);
				if ($laboral['referencias'] == "")
					$ref = "No especific&oacute;";
				else
					$ref = $laboral['referencias'];
				$t->set_var("referencias", $ref);
				getActividad($laboral['actividad'], $acti);
				$t->set_var("actividad", utf8_encode($acti));
				getRamo($laboral['ramo'], $ramoEmpleo);
				$t->set_var("rama", utf8_encode($ramoEmpleo));
				if ($laboral['motivoEgreso'] == "")
					$motivo = "No Especific&oacute;";
				else
					$motivo = $laboral['motivoEgreso'];

				$t->set_var("egreso", $motivo);
				$t->set_var("responsabilidades", $laboral['responsabilidades']);
				if ($laboral['conocimientos'] == "")
					$cono = "No especifi&oacute;";
				else
					$cono = $laboral['conocimientos'];
				$t->set_var("conocimientos", $cono);

				$t->parse("_experiencia", "experiencia", true);
			}
		}
	}
}
}

function ver_cv_new()
{
	global $db,$t;


	if($_GET['usuario'] != $_SESSION['usuario'])
		$t->set_file("pl", "error.htm");
	else {


		$t->set_file("pl", "cv.html");
		$t->set_block("pl", "estudios", "_estudios");
		$t->set_block("pl", "experiencia", "_experiencia");


		$usuario = str_replace(".", "", $_GET['usuario']);

		if (!is_numeric($usuario)) {
			echo "No es un usuario REAL!";
			exit;
		} else {
			$sql = "select * from usuario where dni = " . $usuario . " ORDER BY apellido";

			$sql_estudios = "select * from estudios where dni = " . $usuario;


			$sql_laboral = "SELECT * FROM laboral, jerarquias, ramos  WHERE laboral.dni = " . $usuario . " AND laboral.actividad = jerarquias.id_jerarquia AND ramos.numero_ramo = laboral.ramo";

			$resul_laboral = mysqli_query($db, $sql_laboral);

			$resul_estudio = mysqli_query($db, $sql_estudios);

			$result = mysqli_query($db, $sql);

			//seteo los datos del usuario
			$datos_usuario = mysqli_fetch_array($result);

			$t->set_var("apellido", $datos_usuario['apellido']);
			$t->set_var("nombre", $datos_usuario['nombre']);
			$t->set_var("dni", $datos_usuario['dni']);
			$t->set_var("foto_usuario", $datos_usuario['dni']);
			$t->set_var("mail", $datos_usuario['email']);
			$t->set_var("twitter_user", $datos_usuario['twitter']);
			$t->set_var("facebook_user", $datos_usuario['facebook']);

			if ($datos_usuario['facebook'] != '') {
				$t->set_var("mostrar_face", "block;");
				$t->set_var("link_facebook", $datos_usuario['facebook']);
			} else
				$t->set_var("mostrar_face", "none;");

			if ($datos_usuario['twitter'] != '') {
				$t->set_var("mostrar_linkedin", "block;");
				$t->set_var("link_linkedin", $datos_usuario['twitter']);
			} else
				$t->set_var("mostrar_linkedin", "none;");

			$t->set_var("telefono", $datos_usuario['telefonoContacto']);
			$civil = $datos_usuario["estadoCivil"];
			if ($civil == 1)
				$estado_civil = "Soltero/a";
			elseif ($civil == 2)
				$estado_civil = "Casado/a";
			elseif ($civil == 3)
				$estado_civil = "Concubinato/a";
			elseif ($civil == 4)
				$estado_civil = "Viudo/a";
			$t->set_var("civil", $estado_civil);
			if ($datos_usuario["remuneracion"] == '0')
				$sueldo = "No espeficicó";
			else
				$sueldo = $datos_usuario["remuneracion"] . " Pesos";
			$t->set_var("remuneracion", $sueldo);
			$t->set_var("hijos", $datos_usuario["hijos"]);
			$t->set_var("domicilio", $datos_usuario["domicilio"]);
			$nombreProv = getProvincia($datos_usuario["provincia"]);
			$t->set_var("provincia", $nombreProv);
			$nombreCiu = getCiudad($datos_usuario["ciudad"]);
			$t->set_var("ciudad", $nombreCiu);
			$nombrePai = getPais($datos_usuario["pais"]);
			$t->set_var("pais", $nombrePai);
			$t->set_var("fecha_nacimiento", $datos_usuario["fechaNacimiento"]);

			$idiomasJuntos = "";
			$idiomatmp = "";
			$congresosJuntos = "";
		//estudios
			while ($estudios = mysqli_fetch_array($resul_estudio)) {

				$estudio = getEstudio($estudios["estudio"]);
				$t->set_var("estudio", $estudio);
				$t->set_var("titulo", $estudios["titulo"]);
				$estado = getEstado($estudios["estado"]);
				$t->set_var("estado", $estado);
				$inst = getInstitucion($estudios["institucion"]);
				$t->set_var("institucion", $inst);

				$t->parse("_estudios", "estudios", true);
			}

			$sql_idiomas = "SELECT * FROM idiomas WHERE dni = " . $usuario;
			$resul_idiomas = mysqli_query($db, $sql_idiomas);

			$idiom = "";
			while ($idiomas = mysqli_fetch_array($resul_idiomas))
				$idiom = $idiom . $idiomas['nombre'] . ", <strong>Nivel:</strong> " . $idiomas['nivel'] . " | ";

			$t->set_var("idiomas", $idiom);

			$sql_cursos = "SELECT * FROM cursos WHERE dni = " . $usuario;
			$resul_cursos = mysqli_query($db, $sql_cursos);

			$cur = "";
			while ($cursos = mysqli_fetch_array($resul_cursos))
				$cur = $cur . $cursos['curso'] . ",  ";

			$t->set_var("cursos", $cur);

			$sql_congresos = "SELECT * FROM congresos WHERE dni = " . $usuario;
			$resul_congresos = mysqli_query($db, $sql_congresos);

			$congre = "";
			while ($congresos = mysqli_fetch_array($resul_congresos))
				if ($congresos['congreso'] != "")
					$congre = $congre . $congresos['congreso'] . ",  ";

			if ($congre == "")
				$congre = "No especific&oacute;";

			$t->set_var("congresos", $congre);


// experiencias
			while ($laboral = mysqli_fetch_array($resul_laboral)) {
				if ($laboral['empresa'] != "") {
					$t->set_var("empresa", $laboral['empresa']);
					$t->set_var("desde", $laboral['desde']);
					$t->set_var("hasta", $laboral['hasta']);
					if ($laboral['referencias'] == "")
						$ref = "No especific&oacute;";
					else
						$ref = $laboral['referencias'];
					$t->set_var("referencias", $ref);
					getActividad($laboral['actividad'], $acti);
					$t->set_var("actividad", $acti);
					getRamo($laboral['ramo'], $ramoEmpleo);
					$t->set_var("rama", $ramoEmpleo);
					if ($laboral['motivoEgreso'] == "")
						$motivo = "No Especific&oacute;";
					else
						$motivo = $laboral['motivoEgreso'];

					$t->set_var("egreso", $motivo);
					$t->set_var("responsabilidades", $laboral['responsabilidades']);
					if ($laboral['conocimientos'] == "")
						$cono = "No especifi&oacute;";
					else
						$cono = $laboral['conocimientos'];
					$t->set_var("conocimientos", $cono);

					$t->parse("_experiencia", "experiencia", true);
				}
			}
		}
	}
}


function clientes()
{
	global $db,$t;
	
	$t->set_file("pl", "empresas.html");
	parsear_logos_empresas();
	
}



function ingresarCv()
{
global $db,$t;
	
	$t->set_file("pl", "curriculum.htm");
	parsear_logos_empresas();

}

function ingresarCv_new()
{
global $db,$t;
	
	$t->set_file("pl", "my-account.html");
	parsear_logos_empresas();

	if($_GET["error"] == 1)
		$t->set_var("mensaje", "<strong style='color:red'>El USUARIO YA EXSITE!</strong>");
	if($_GET["mensaje"] == "enviado")
		$t->set_var("mensaje", "<strong style='color:green'>Enviamos un email con la nueva contraseña a tu email, revisa la carpeta SPAM!</strong>");
	elseif($_GET["mensaje"] == "noenviado")
		$t->set_var("mensaje", "<strong style='color:red'>¡Los datos del DNI y el Email no coinciden!, usa el email de registro!</strong>");

}

function registrarce()
{
global $db,$t,$path_site;
	
	$nuevo_usuario =  $_POST['username'];
	$email = $_POST['email'];
	$pass = md5($_POST['new_password']);
	
	$sql = "select * from usuario where dni = '".$nuevo_usuario."'";

	
	$fecha_actual  = date("Y-m-d");
	
	$result = mysqli_query($db,$sql); 
	
	$numero = mysqli_num_rows($result);

	if($numero == 0 && $nuevo_usuario != "")
		{
			$t->set_file("pl", "curriculum-cv-1.htm");
			$datos_personales = mysqli_fetch_array($result);
			cargar_provincias($datos_personales['provincia']);
			cargar_ciudades($datos_personales['ciudad']);
			cargar_paises($datos_personales['pais']);
			parsear_sexos("");
			cargar_documentos($datos_personales['tipoDocumento']);
			cargar_estado_civil($datos_personales['estadoCivil']);
			$t->set_var("fondo","_form");
			$t->set_var("dni", $nuevo_usuario);
			$t->set_var("email", $email);
			
			//session_register("usuario");
			$_SESSION['usuario'] = $nuevo_usuario;
			
			$sql = "insert into usuario (dni, pass, email, alta) values ($nuevo_usuario , '".$pass."', '".$email."', '".$fecha_actual."')";
			mysqli_query($db,$sql);

			$directorio = $path_site."/usuarios/".$nuevo_usuario;
			
			//echo $directorio; exit;
			
			mail_bienvenida($nuevo_usuario,$email);
			
			mkdir($directorio);
			

		}
	else
		{
			header("Location: /?action=ingresar_cv&error=1#tab2");
			exit;
		}
	

}

function cargar_provincias($seleccionada)
{
global $db,$t;

$t->set_block("pl", "provincias","_provincias");

$sql = "SELECT * FROM provincias";

$prov = mysqli_query($db,$sql);

if($seleccionada == "")
	$seleccionada = 107;

while($provincias = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $provincias['numero_provincia'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("provincia", utf8_encode($provincias['nombre_provincia']));
	$t->set_var("num_provincia", $provincias['numero_provincia']);
	$t->parse("_provincias", "provincias",true);
}

}

function cargar_provincias_new($seleccionada,$t)
{
global $db;

$t->set_block("pl", "provincias","_provincias");

$sql = "SELECT * FROM provincias";

$prov = mysqli_query($db,$sql);

if($seleccionada == "")
	$seleccionada = 107;

while($provincias = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $provincias['numero_provincia'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("provincia", utf8_encode($provincias['nombre_provincia']));
	$t->set_var("num_provincia", $provincias['numero_provincia']);
	$t->parse("_provincias", "provincias",true);
}

}


function cargar_institucion($seleccionada)
{
global $db,$t;

$t->set_block("pl", "instituciones","_instituciones");

$sql = "SELECT * FROM universidades";

$prov = mysqli_query($db,$sql);

while($provincias = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $provincias['numero_universidad'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("institucion", utf8_encode($provincias['nombre_universidad']));
	$t->set_var("num_institucion", $provincias['numero_universidad']);
	$t->parse("_instituciones", "instituciones",true);
}

}


function cargar_ciudades($seleccionada)
{
global $db,$t;

$t->set_block("pl", "ciudades","_ciudades");

$sql = "SELECT distinct numero_ciudad, nombre_ciudad  FROM ciudades order by nombre_ciudad ASC";

$ciudad = mysqli_query($db,$sql);

while($ciudades = mysqli_fetch_array($ciudad))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $ciudades['numero_ciudad'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("ciudad",  $ciudades['nombre_ciudad']);
	$t->set_var("num_ciudad", $ciudades['numero_ciudad']);
	$t->parse("_ciudades", "ciudades",true);
}

}

function cargar_areas($seleccionada)
{
global $db,$t;

$t->set_block("pl", "areas","_areas");

$sql = "SELECT * FROM areas";

$prov = mysqli_query($db,$sql);

while($areas = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $areas['numero_area'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("area",  utf8_encode($areas['nombre_area']));
	$t->set_var("num_area", $areas['numero_area']);
	$t->parse("_areas", "areas",true);
}

}

function cargar_areas_new($seleccionada,$t)
{
global $db;

$t->set_block("pl", "areas","_areas");

$sql = "SELECT * FROM areas";

$prov = mysqli_query($db,$sql);

while($areas = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $areas['numero_area'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("area",  utf8_encode($areas['nombre_area']));
	$t->set_var("num_area", $areas['numero_area']);
	$t->parse("_areas", "areas",true);
}

}


function cargar_jerarquias($seleccionada)
{
global $db,$t;

$t->set_block("pl", "jerarquias","_jerarquias");

$sql = "SELECT * FROM jerarquias";

$prov = mysqli_query($db,$sql);

while($jerarquias = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $jerarquias['id_jerarquia'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("jerarquia",  utf8_encode($jerarquias['nombre_jerarquia']));
	$t->set_var("num_jerarquia", $jerarquias['id_jerarquia']);
	$t->parse("_jerarquias", "jerarquias",true);
}

}


function cargar_niveles($seleccionada)
{
global $db,$t;

$t->set_block("pl", "niveles_esutios","_niveles_esutios");

$sql = "SELECT * FROM niveles_idiomas order by nivel";

$niv = mysqli_query($db,$sql);

while($niveles = mysqli_fetch_array($niv))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $niveles['nivel'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("nombre_nivel", utf8_encode($niveles['nivel']));
	$t->parse("_niveles_esutios", "niveles_esutios",true);
}

}

function cargar_niveles_idiomas($seleccionada, $t)
{
global $db;

$sql = "SELECT * FROM niveles_idiomas order by nivel";

$niv = mysqli_query($db,$sql);

while($niveles = mysqli_fetch_array($niv))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $niveles['nivel'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("nombre_nivel", utf8_encode($niveles['nivel']));
	$t->parse("_niveles_idiomas", "niveles_idiomas",true);
}



}



function cargar_tipos_estudios($seleccionada)
{
global $db,$t;

$t->set_block("pl", "tipo_esutios","_tipo_esutios");

$sql = "SELECT * FROM tipo_estudio";

$prov = mysqli_query($db,$sql);

while($estudios = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $estudios['numero_estudio'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("nombre_estudio",  utf8_encode($estudios['nombre_estudio']));
	$t->set_var("numero_estudio", $estudios['numero_estudio']);
	$t->parse("_tipo_esutios", "tipo_esutios",true);
}

}

function cargar_estados_estados($seleccionada)
{
global $db,$t;

$t->set_block("pl", "estados_estudio","_estados_estudio");

$sql = "SELECT * FROM estados";

$prov = mysqli_query($db,$sql);

while($estado = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $estado['numero_estado'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("nombre_estado",  utf8_encode($estado['nombre_estado']));
	$t->set_var("numero_estado", $estado['numero_estado']);
	$t->parse("_estados_estudio", "estados_estudio",true);
}

}

function cargar_ofertas_home_ultimos()
{
global $db,$t;
 
$importante_empresa = "importanteempresa";

$t->set_block("pl", "listado_ofertas","_listado_ofertas");

require_once 'PHPPaging.lib.php';

$paging = new PHPPaging;


$sql = "SELECT publicacion FROM empleos ORDER BY publicacion DESC LIMIT 1";

$fech = mysqli_query($db,$sql);

$dia = mysqli_fetch_array($fech);

$hoy = $dia['publicacion'];

$dias= 21; // los d�as a restar
//$dias= 145; // los d�as a restar

$una_semana =  date("Y-m-d", strtotime("$hoy -$dias day"));  

$paging->agregarConsulta("SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND  empleos.publicacion BETWEEN '".$una_semana."' AND '".$hoy."' AND empresas.id_empresa = empleos.id_empresa and empleos.activo = 1 and empleos.id_empresa != 175 order by empleos.tipo asc, empleos.activo desc, empleos.publicacion desc, rand()");



$paging->porPagina(30);
$paging->linkClase("link_verde");
$paging->ejecutar($db);

while($datos = $paging->fetchResultado()) 
{
	// busco el nombre del puesto de trabajo
	$sql_p_trabajo = "SELECT * FROM puestos_trabajo WHERE numero_puesto = ".$datos['puesto_trabajo'];
	
	//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8");

	
	$puesTrabajo = mysqli_query($db,$sql_p_trabajo);
	
	$p_trabajo = mysqli_fetch_array($puesTrabajo);
	
	
	if (strlen($p_trabajo['nombre_puesto']) >= 34)
		$pustotito_trabajo = substr($p_trabajo['nombre_puesto']."...", 0, 34);
	else	
		$pustotito_trabajo = $p_trabajo['nombre_puesto'];
		
	$t->set_var("puesto_trabajo", utf8_encode($pustotito_trabajo));
		
	if (strlen($datos['titulo']) >= 18)
		$titulo_empleo = substr($datos['titulo']." ...", 0, 22);
	else
		$titulo_empleo = $datos['titulo'];	
	
	$t->set_var("titulo_trabajo", $titulo_empleo);

	if($datos['mlogo'] != 0)
		$t->set_var("logo", $datos['logo']);
	else
		$t->set_var("logo", $importante_empresa);
			
	$t->set_var("id_puesto", $datos['id_empleo']);
	
	if($datos['nuevo'] == 0)
		$t->set_var("ciudad", utf8_encode($datos['nombre_ciudad']));
	else
		$t->set_var("ciudad", utf8_encode($datos['nombre_ciudad']));
	
	
	//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$datos['disponibilidad'];
	//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8"); 
	$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
	$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);
	
	$t->set_var("dispo", $nombre_disponibilidad["nombre_disponibilidad"]);
	
	//detalle nuevo en la home
	if($datos['nuevo'] == 0)
		$enlace = "<a href='/empleo-". str_replace(" ","-",$datos['titulo'])."-en-".str_replace(" ","-",utf8_encode($datos['nombre_ciudad']))."/".$datos['id_empleo']."' title='Ver Mas' target='_blank'>"; // para el enlace de la home
	else
		$enlace = "<a href='/empleo-". str_replace(" ","-",$datos['titulo'])."-en-".str_replace(" ","-",$datos['nombre_ciudad'])."/".$datos['id_empleo']."' title='Ver Mas' target='_blank'>"; // para el enlace de la home
				
	// solo los empleos premium  en la home
	$t->set_var("ver_home", $enlace);
	$t->parse("_listado_ofertas", "listado_ofertas",true);

}
    // Imprimimos la barra de navegaci�n
    $t->set_var('paginacion',$paging->fetchNavegacion());
	
}


function cargar_ofertas_home_express()
{
global $db,$t;
 
$importante_empresa = "importanteempresa";

$t->set_block("pl", "listado_ofertas_express","_listado_ofertas_express");

require_once 'PHPPaging.lib.php';

$paging = new PHPPaging;


$paging->agregarConsulta("SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND empresas.id_empresa = empleos.id_empresa and empleos.activo = 1 and empresas.id_empresa = 175 order by empleos.tipo asc, empleos.activo desc, empleos.publicacion desc");



$paging->porPagina(30);
$paging->linkClase("link_verde");
$paging->ejecutar($db);

while($datos = $paging->fetchResultado()) 
{
	// busco el nombre del puesto de trabajo
	$sql_p_trabajo = "SELECT * FROM puestos_trabajo WHERE numero_puesto = ".$datos['puesto_trabajo'];
	
	//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8");
	
	$puesTrabajo = mysqli_query($db,$sql_p_trabajo);
	
	$p_trabajo = mysqli_fetch_array($puesTrabajo);
	
	
	if (strlen($p_trabajo['nombre_puesto']) >= 34)
		$pustotito_trabajo = substr($p_trabajo['nombre_puesto']."...", 0, 34);
	else	
		$pustotito_trabajo = $p_trabajo['nombre_puesto'];
		
	$t->set_var("puesto_trabajo_express", utf8_encode($pustotito_trabajo));
		
	if (strlen($datos['titulo']) >= 18)
		$titulo_empleo = substr($datos['titulo']." ...", 0, 22);
	else
		$titulo_empleo = $datos['titulo'];	
	
	$t->set_var("titulo_trabajo_express", $titulo_empleo);

	if($datos['mlogo'] == 0)
		$t->set_var("logo_express", $importante_empresa);
	else
		$t->set_var("logo_express", $datos['logo']);

			
	$t->set_var("id_puesto_express", $datos['id_empleo']);
	
	if($datos['nuevo'] == 0)
		$t->set_var("ciudad_express", utf8_encode($datos['nombre_ciudad']));
	else
		$t->set_var("ciudad_express", utf8_encode($datos['nombre_ciudad']));	
	
	
	//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$datos['disponibilidad'];
	//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8");
	$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
	$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);
	
	$t->set_var("dispo_express", $nombre_disponibilidad["nombre_disponibilidad"]);
	
	//detalle nuevo en la home
	if($datos['nuevo'] == 0)
		$enlace = "<a href='/empleo-". normalizar(str_replace(" ","-",$datos['titulo'])."-en-".str_replace(" ","-",trim(utf8_encode($datos['nombre_ciudad']))))."/".$datos['id_empleo']."' title='Ver Mas' target='_blank'>"; // para el enlace de la home
	else
		$enlace = "<a href='/empleo-". normalizar(str_replace(" ","-",$datos['titulo'])."-en-".str_replace(" ","-",trim($datos['nombre_ciudad'])))."/".$datos['id_empleo']."' title='Ver Mas' target='_blank'>"; // para el enlace de la home
				
	// solo los empleos premium  en la home
	$t->set_var("ver_home_express", $enlace);
	$t->parse("_listado_ofertas_express", "listado_ofertas_express",true);

}
    // Imprimimos la barra de navegaci�n
    $t->set_var('paginacion_express',$paging->fetchNavegacion());
	
}

function cargar_ofertas_home_express_new($widget)
{
global $db,$tn;

$t = $tn;
$importante_empresa = "importanteempresa";

if($widget == 1)
	$t->set_block("pl", "listado_ofertas_express_der","_listado_ofertas_express_der");
else
{
	$t->set_block("pl", "listado_ofertas_express","_listado_ofertas_express");
	$t->set_block("pl", "listado_ofertas_express_der","_listado_ofertas_express_der");
}


$sql = "SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND empresas.id_empresa = empleos.id_empresa and empleos.activo = 1 and empresas.id_empresa = 175 order by empleos.tipo asc, empleos.activo desc, empleos.publicacion desc ";

	//$db->set_charset("latin1");
$result = mysqli_query($db,$sql);
	$cant_resultado = mysqli_num_rows($result);
	if($cant_resultado == 0)
		$t->set_var("no_tiene_express", "style='display:none;'");


	while($row = mysqli_fetch_array($result))
	{
		//$titulo_empleo = ucfirst(strtolower(utf8_encode($row["titulo"])));
		if(mb_detect_encoding($row["titulo"], "auto") != "UTF-8")
			$titulo_empleo = ucfirst(strtolower(utf8_encode($row["titulo"])));
		else
		{
			$titulo_empleo = ucfirst(strtolower($row["titulo"]));
		}

		$t->set_var("titulo_empleo",$titulo_empleo);
		$city = get_ciudad($db,$row["ciudad"]);
		if(mb_detect_encoding($city, "auto") != "UTF-8")
			$ciudad_empleo = utf8_encode($city);
		else
			$ciudad_empleo = $city;
		$provincia_empleo = get_provincia($db,$row["provincia"]);
		if($row['mlogo'] == 0)
			$t->set_var("logo", $importante_empresa);
		else
			$t->set_var("logo", $row['logo']);
		$id_empleo = $row["id_empleo"];
		$t->set_var("ciudad_empleo",$provincia_empleo.", ".$ciudad_empleo);
		$t->set_var("publicacion",$row['publicacion']);
		
		//busco el nombre del tipo de disponibilidad
		$sql_disponibilidad = "SELECT nombre_disponibilidad FROM disponibilidades WHERE num_disponibilidad = ".$row['disponibilidad'];
		//mysqli_query("SET NAMES 'utf8'"); 
		$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
		$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);

		$t->set_var("jornada_empleo", $nombre_disponibilidad["nombre_disponibilidad"]);
		
		// busco el nombre del puesto de trabajo
		$sql_p_trabajo = "SELECT nombre_puesto FROM puestos_trabajo WHERE numero_puesto = ".$row['puesto_trabajo'];
		//$db->set_charset("latin1");
		//mysql_query("SET NAMES 'utf8'");

		$puesTrabajo = mysqli_query($db,$sql_p_trabajo);

		$p_trabajo = mysqli_fetch_array($puesTrabajo);

		if (strlen($p_trabajo['nombre_puesto']) >= 34)
			$pustotito_trabajo = substr($p_trabajo['nombre_puesto']."...", 0, 34);
		else	
			$pustotito_trabajo = $p_trabajo['nombre_puesto'];
		
		if($row['disponibilidad'] == 2 ) $t->set_var("color_jornada", "full-time");
		else $t->set_var("color_jornada", "part-time");

		$t->set_var("puesto_trabajo", utf8_encode($pustotito_trabajo));
		$enlace = "<a href='/empleo-". normalizar(str_replace(" ","-",$titulo_empleo)."-en-".str_replace(" ","-",$ciudad_empleo))."/".$id_empleo."' title='Ver Mas' target='_blank'>"; // para el enlace de la home
		$enlace_a = "<a href='/empleo-". normalizar(str_replace(" ","-",$titulo_empleo)."-en-".str_replace(" ","-",$ciudad_empleo))."/".$id_empleo."' title='Ver Mas' class='button' target='_blank'>"; // para el enlace de la home
		$t->set_var("ver_home_express", utf8_encode($enlace));
		$t->set_var("ver_home_express_a", utf8_encode($enlace_a));

		if($widget == 1)
			$t->parse("_listado_ofertas_express_der", "listado_ofertas_express_der",true);
		else
		{
			$t->parse("_listado_ofertas_express", "listado_ofertas_express",true);
			$t->parse("_listado_ofertas_express_der", "listado_ofertas_express_der",true);
		}

	}
	
}

function cargar_ofertas_home()
{
global $db,$t;
 
$importante_empresa = "importanteempresa";

$t->set_block("pl", "listado_ofertas","_listado_ofertas");

require_once 'PHPPaging.lib.php';

$paging = new PHPPaging;


$sql = "SELECT publicacion FROM empleos ORDER BY publicacion DESC LIMIT 1,1";

$fech = mysqli_query($db,$sql);

$dia = mysqli_fetch_array($fech);

$hoy = $dia['publicacion'];

$dias= 7; // los d�as a restar

$una_semana =  date("Y-m-d", strtotime("$hoy -$dias day"));  

//$paging->agregarConsulta("SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND  empleos.publicacion BETWEEN '".$una_semana."' AND '".$hoy."' AND empresas.id_empresa = empleos.id_empresa and empleos.activo = 1 order by empleos.tipo asc, empleos.activo desc, empleos.publicacion desc, rand()");

$paging->agregarConsulta("SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad  AND empresas.id_empresa = empleos.id_empresa and empleos.activo = 1  and empleos.id_empresa != 175 order by empleos.tipo asc, empleos.activo desc, rand()");


$paging->porPagina(30);
$paging->linkClase("link_verde");
$paging->ejecutar($db);

while($datos = $paging->fetchResultado()) 
{
	// busco el nombre del puesto de trabajo
	$sql_p_trabajo = "SELECT * FROM puestos_trabajo WHERE numero_puesto = ".$datos['puesto_trabajo'];
	
	//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8");
	
	$puesTrabajo = mysqli_query($db,$sql_p_trabajo);
	
	$p_trabajo = mysqli_fetch_array($puesTrabajo);
	
	if (strlen($p_trabajo['nombre_puesto']) >= 34)
		$pustotito_trabajo = substr($p_trabajo['nombre_puesto']."...", 0, 34);
	else	
		$pustotito_trabajo = $p_trabajo['nombre_puesto'];
		
	$t->set_var("puesto_trabajo", utf8_encode($pustotito_trabajo));
	
	if (strlen($datos['titulo']) >= 18)
		$titulo_empleo = substr($datos['titulo']." ...", 0, 22);
	else
		$titulo_empleo = $datos['titulo'];	
	
	$t->set_var("titulo_trabajo", $titulo_empleo);

	if($datos['mlogo'] != 0)
		$t->set_var("logo", $datos['logo']);
	else
		$t->set_var("logo", $importante_empresa);
			
	$t->set_var("id_puesto", $datos['id_empleo']);
	
	if($datos['nuevo'] == 0)
		$t->set_var("ciudad", utf8_encode($datos['nombre_ciudad']));
	else
		$t->set_var("ciudad", utf8_encode($datos['nombre_ciudad']));	
	
	
	//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$datos['disponibilidad'];
	//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8");
	$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
	$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);
	
	$t->set_var("dispo", $nombre_disponibilidad["nombre_disponibilidad"]);
	
	//detalle nuevo en la home
	if($datos['nuevo'] == 0)
		$enlace = "<a href='/empleo-". normalizar(str_replace(" ","-",$datos['titulo'])."-en-".str_replace(" ","-",utf8_encode($datos['nombre_ciudad'])))."/".$datos['id_empleo']."' title='Ver Mas' target='_blank'>"; // para el enlace de la home
	else
		$enlace = "<a href='/empleo-". normalizar(str_replace(" ","-",$datos['titulo'])."-en-".str_replace(" ","-",$datos['nombre_ciudad']))."/".$datos['id_empleo']."' title='Ver Mas' target='_blank'>"; // para el enlace de la home
				
	// solo los empleos premium  en la home
	$t->set_var("ver_home", $enlace);
	$t->parse("_listado_ofertas", "listado_ofertas",true);

}
    // Imprimimos la barra de navegaci�n
    $t->set_var('paginacion',$paging->fetchNavegacion());
	
}

function cargar_ofertas_home_new()
{
global $db,$tn;

$t = $tn;
 
$importante_empresa = "importanteempresa";

$t->set_block("pl", "listado_ofertas","_listado_ofertas");


$sql = "SELECT publicacion FROM empleos ORDER BY publicacion DESC LIMIT 1,1";

$fech = mysqli_query($db,$sql);

$dia = mysqli_fetch_array($fech);

$hoy = $dia['publicacion'];

$dias= 7; // los días a restar

$una_semana =  date("Y-m-d", strtotime("$hoy -$dias day"));  

//$paging->agregarConsulta("SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND  empleos.publicacion BETWEEN '".$una_semana."' AND '".$hoy."' AND empresas.id_empresa = empleos.id_empresa and empleos.activo = 1 order by empleos.tipo asc, empleos.activo desc, empleos.publicacion desc, rand()");

$query = "SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad  AND empresas.id_empresa = empleos.id_empresa and empleos.activo = 1  and empleos.id_empresa != 175 order by empleos.publicacion desc, empleos.tipo asc limit 21";
//$db->set_charset("latin1");
$resultado_query = mysqli_query($db,$query);
$importante_empresa = "importanteempresa";


while($datos = mysqli_fetch_array($resultado_query)) 
{
	// busco el nombre del puesto de trabajo
	$sql_p_trabajo = "SELECT * FROM puestos_trabajo WHERE numero_puesto = ".$datos['puesto_trabajo'];
	
	//mysqli_query("SET NAMES 'utf8'");
	//$db->set_charset("latin1");
	
	$puesTrabajo = mysqli_query($db,$sql_p_trabajo);
	
	$p_trabajo = mysqli_fetch_array($puesTrabajo);
	
	if (strlen($p_trabajo['nombre_puesto']) >= 34)
		$pustotito_trabajo = substr($p_trabajo['nombre_puesto']."...", 0, 34);
	else	
		$pustotito_trabajo = $p_trabajo['nombre_puesto'];
		
	$t->set_var("puesto_trabajo", utf8_encode($pustotito_trabajo));
	
	if (strlen($datos['titulo']) >= 55)
		$titulo_empleo = substr($datos['titulo']." ...", 0, 58);
	else
		$titulo_empleo = $datos['titulo'];

	//$tipo_caracter = mb_detect_encoding($titulo_empleo, "auto");

	if(mb_detect_encoding($titulo_empleo, "auto") != "UTF-8")
		$titulo_codificado = ucfirst(strtolower(utf8_encode($titulo_empleo)));
	else
		$titulo_codificado = ucfirst(strtolower($titulo_empleo));

		
	$t->set_var("titulo_trabajo", $titulo_codificado);

	if($datos['mlogo'] == 0)
		$t->set_var("logo", $importante_empresa);
	else
		$t->set_var("logo", $datos['logo']);


	$t->set_var("id_puesto", $datos['id_empleo']);
	
	$hoy_mostrar = date("d-m-Y", strtotime($datos['publicacion']));
		
	$t->set_var("publicacion",$hoy_mostrar);
	
	//if($tipo_caracter_ciudad == "utf-8")
	$city = $datos['nombre_ciudad'];
	if(mb_detect_encoding($city, "auto") != "UTF-8")
		$ciudad_empleo = utf8_encode($city);
	else
		$ciudad_empleo = $city;
	//$ciudad_empleo = utf8_encode($datos['nombre_ciudad']);
		$t->set_var("ciudad", $ciudad_empleo);
	//else
	//	$t->set_var("ciudad", utf8_encode($datos['nombre_ciudad']));	

	
	//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$datos['disponibilidad'];
	//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8");
	$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
	$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);

	if($datos['disponibilidad'] == 2 )
	{
		$t->set_var("color_jornada", "full-time");
	}
	else {
		$t->set_var("color_jornada", "part-time");
	}
	
	$t->set_var("dispo", $nombre_disponibilidad["nombre_disponibilidad"]);
	
	//detalle nuevo en la home
	if($datos['nuevo'] == 0){
		$link = normalizar(trim($titulo_codificado)."-en-".trim($ciudad_empleo));
		$utl_amigable = trim(normalizar($link));
		$enlace = "<a href='/empleo-". $utl_amigable."/".$datos['id_empleo']."' title='Ver más sobre ".$titulo_codificado."' target='_blank'>"; // para el enlace de la home
		}
	else
	{
		$link = normalizar(trim($titulo_codificado."-en-".trim($ciudad_empleo)));
		$utl_amigable = trim(normalizar($link));
		$enlace = "<a href='/empleo-".$utl_amigable."/".$datos['id_empleo']."' title='Ver más sobre ".$titulo_codificado."' target='_blank'>"; // para el enlace de la home
	}
				
	// solo los empleos premium  en la home
	$t->set_var("ver_home", strtolower($enlace));
	$t->parse("_listado_ofertas", "listado_ofertas",true);

}
 }

 function normalizar($cadena) {
	$cad = strtolower($cadena);		
	$cadena = str_replace(
		array('À','Á','Â','Ã','Ä','Å','à','á','â','ã','ä','å','Ò','Ó','Ô','Õ','Ö','Ø','ò','ó','ô','õ','ö','ø','€','È','É','Ê','Ë','è','é','ê','ë','Ç','ç','Ì','Í','Î','Ï','ì','í','î','ï','Ù','Ú','Û','Ü','ù','ú','û','ü','ÿ','Ñ','ñ','/','´','`','&','¬','º','!','¡','¿','?','·','’','ª','™','®','²','°','µ','«','»','±','²','>','<','ä','ë','ï','ö','ü','¨','ª','+','(',')','½','©','“','”','æ','©','³','¶','¤','¦','Æ','¥','ÿ','§','œ','ß','Œ','ð','¹','Ý','¼','¾','£','Þ',' ','%','*','.','%'),
		array('a','a','a','a','a','a','a','a','a','a','a','a','o','o','o','o','o','o','o','o','o','o','o','o','e','e','e','e','e','e','e','e','e','c','c','i','i','i','i','i','i','i','i','u','u','u','u','u','u','u','u','y','n','n','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','u','-','-','-','-','-','-','a','e','i','o','u','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','-','y','-','-','-','-','-','-','y','-','-','-','-','-','-','','','-'),
		$cad);
	$cadena =  str_replace("\"","-",$cadena);
	$cadena =  str_replace("---","-",$cadena);
	$cadena =  str_replace("--","-",$cadena);
	
	return($cadena);
}  


function cargar_ofertas($seleccionada, $sql_post)
{
global $db,$t;

$importante_empresa = "importanteempresa";

$t->set_block("pl", "listado_ofertas","_listado_ofertas");

if(isset($_REQUEST['city']))
	$ciudad = " and ciudades.numero_ciudad = ".$_REQUEST['ciudad']." ";
else
	$ciudad = "";
		
if(isset($_GET['city']) || $sql_post != "") {
	$sql = $sql_post . " order by empleos.tipo asc, empleos.publicacion desc ";

}
elseif($sql_post == "") {
	$sql = "SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND empleos.activo = 1 and empresas.id_empresa = empleos.id_empresa " . $ciudad . "  order by empleos.tipo asc, empleos.activo desc, empleos.publicacion desc ";

}

	//$db->set_charset("latin1");

	require_once 'PHPPaging.lib.php';

	$paging = new PHPPaging;

	$paging->agregarConsulta($sql);
	$paging->porPagina(20);
	$paging->linkClase("link_verde");
	$paging->ejecutar($db);
	$city = "&ciudad=".$_REQUEST['ciudad'];
	$prov = "&provincia=".$_REQUEST['provincia'];
	if(isset($_REQUEST['lateral']))
		$are = "&area=".$_REQUEST['lateral'];
	else	
		$are = "&area=".$_REQUEST['areas'];
	$paging->linkAgregar($city.$prov.$are);
	//$paging->linkAgregar($provincia);


while($ofertas = $paging->fetchResultado())
{

	
	$t->set_var("ciudad",  $ofertas['nombre_ciudad']);
	
	if($ofertas['mlogo'] == 1)	
		$empresa = "<strong >Empresa: </strong> ".$ofertas['razon_social'];
	else
		$empresa = "<strong >Empresa: </strong> ".$ofertas['asunto'];	
	
	$t->set_var("empresa", $empresa); // si es gold se parsea el nombre de la empresa sino el sector
		
	// busco el nombre del puesto de trabajo
	$sql_p_trabajo = "SELECT * FROM puestos_trabajo WHERE numero_puesto = ".$ofertas['puesto_trabajo'];
	//mysqli_query("SET NAMES 'utf8'");
	//$db->set_charset("utf8");
	$db->set_charset("latin1");
	$puesTrabajo = mysqli_query($db,$sql_p_trabajo);
	$p_trabajo = mysqli_fetch_array($puesTrabajo);

	if(mb_detect_encoding($ofertas['titulo'], "auto") != "UTF-8")
		$titulo_empleo = ucfirst(strtolower(utf8_encode($ofertas['titulo'])));
	else
	{
		$titulo_empleo = ucfirst(strtolower($ofertas['titulo']));
	}
	
	$parse_sector = "<strong> <span style='color:#7e7e7e'>".$titulo_empleo."</span></strong><span style='color:#adb522; font-weight:bold;'>&nbsp;|</span>";
		
	$t->set_var("puesto_trabajo", $p_trabajo['nombre_puesto']);
	$t->set_var("titulo_trabajo", $titulo_empleo);
	$t->set_var("fecha", $ofertas['publicacion']);


	$t->set_var("sector", $parse_sector);
	
	if($ofertas['mlogo'] == 1)
		$t->set_var("logo", $ofertas['logo']);
	else
		$t->set_var("logo", $importante_empresa);
			
	$t->set_var("id_puesto", $ofertas['id_empleo']);
	
	//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$ofertas['disponibilidad'];
	//mysqli_query("SET NAMES 'utf8'");
	//$db->set_charset("utf8");
	$db->set_charset("latin1");

	$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
	$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);
	
	$t->set_var("dispo", $nombre_disponibilidad["nombre_disponibilidad"]);

	if($nombre_disponibilidad["nombre_disponibilidad"] == "Full-Time" ) $t->set_var("color_jornada", "full-time");
	else $t->set_var("color_jornada", "part-time");

	if($ofertas['nuevo'] == 0)
		$ver_mas = "<a href='{empleo_potulado}". str_replace(" ","-",normalizar($titulo_empleo))."-en-".trim(str_replace(" ","-",normalizar($ofertas['nombre_ciudad'])))."/".$ofertas['id_empleo']."' title='Ver más sobre el trabajo de ".str_replace(" ","-",$ofertas['titulo'])."' {face} target='_blank'";
	else
		$ver_mas = "<a href='{empleo_potulado}". str_replace(" ","-",normalizar($titulo_empleo))."-en-".trim(str_replace(" ","-",normalizar($ofertas['nombre_ciudad'])))."/".$ofertas['id_empleo']."' title='Ver más sobre el trabajo de ".str_replace(" ","-",$ofertas['titulo'])."' {face} target='_blank'";

	//tipos de empleos
	if($ofertas['tipo'] == 1)
		{
			$detalle_ofertas = substr(strip_tags($ofertas['detalle']), 0, 120); // parsea el detalle de un empleo
			$t->set_var("tipo_aviso", "oferta_gold");
			$t->set_var("detalle", $detalle_ofertas."..");
			$t->set_var("ver", $ver_mas);
		}	
	elseif($ofertas['tipo'] == 2)
		{
			$detalle_ofertas = substr($ofertas['detalle'], 0, 120); // parsea el detalle de un empleo
			$t->set_var("tipo_aviso", "oferta_plata");
			$t->set_var("empresa", $empresa);
			$detail_ofert = $detalle_ofertas.".. ".$ver_mas;
			$t->set_var("detalle", $detail_ofert);
			$t->set_var("ver", "");
		}	
	elseif($ofertas['tipo'] == 3)
		{
			$detalle_ofertas = substr($ofertas['detalle'], 0, 80); // parsea el detalle de un empleo
			$t->set_var("tipo_aviso", "oferta_bronce");
			$t->set_var("empresa", $parse_sector);	
			$detail_ofert = $detalle_ofertas.".. ".$ver_mas;
			$t->set_var("detalle", $detail_ofert);
			$t->set_var("sector", "");
			$t->set_var("ver", "");
		}
	else
		{
			$t->set_var("tipo_aviso", "oferta");
			$detalle_ofertas = substr($ofertas['detalle'], 0, 20); // parsea el detalle de un empleo
			$detail_ofert = $detalle_ofertas.".. ".$ver_mas;
			$t->set_var("empresa", $parse_sector." " .$detail_ofert );	
			$t->set_var("detalle", "");
			$t->set_var("sector", "");
			$t->set_var("ver", "");
		}

	$t->set_var("empleo_potulado","/empleo-");

	$t->parse("_listado_ofertas", "listado_ofertas",true);	
	 
}

	$t->set_var("paginacion",$paging->fetchNavegacion());
	
}

function nueva_empresa()
{
global $db,$t;

$t->set_file("pl", "nueva_empresa.htm");


}


function alta_empresa()
{
global $db,$t;

$t->set_file("pl", "alta_empresa.htm");
parsear_logos_empresas_limit(18); 	

if(isset($_GET['send'])) {
	if ($_GET['send'] == 1) {
		$t->set_var("textook", "<span style='color:green'>Ya enviamos tu sulicitud de alta de empresa, en breve nos contactaremos para brindarte tu usuario y contraseña si todo es correcto!</span>");
	} else {
		$t->set_var("textook", "<span style='color:red'>Hay algun error en los datos enviados, por favor revise los campos y vuelva a intentarlo!</span>");
	}
}



}

function alta_empresa_new()
{
	global $db, $t;

	$t->set_file("pl", "alta_empresa.html");
	parsear_logos_empresas_limit(18);

	if (isset($_GET['send'])) {
		if ($_GET['send'] == 1) {
			$t->set_var("textook", "<span style='color:green;text-align: center;
    width: 100%;
    float: left;
    margin-bottom: 15px;'>Ya enviamos tu sulicitud de alta de empresa, en breve nos contactaremos para brindarte tu usuario y contraseña si todo es correcto!</span>");
		} else {
			$t->set_var("textook", "<span style='color:red;text-align: center;
    width: 100%;
    float: left;
    margin-bottom: 15px;'>Hay algun error en los datos enviados, por favor revise los campos y vuelva a intentarlo!</span>");
		}
	}
}




function enviar_empresa_captcha()
{
	global $db, $t;

	if (isset($_POST['g-recaptcha-response'])) {
		$captcha_response = true;
		$recaptcha = $_POST['g-recaptcha-response'];

		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array(
			'secret' => '6LcQ0ZUbAAAAABr-5XAjSI5ZwMwGdWvH-P-Jav6W',
			'response' => $recaptcha
		);
		$options = array(
			'http' => array(
				'method' => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context = stream_context_create($options);
		$verify = file_get_contents($url, false, $context);
		$captcha_success = json_decode($verify);
		$captcha_response = $captcha_success->success;

		$razon = $_POST['razonsocial'];
		$mail = $_POST['email'];
		$cuil = $_POST['cuit'];
		$direccion = $_POST['direccion'];
		$contacto = $_POST['contacto'];
		$web = $_POST['web'];
		$intruso = $_GET['mail'];
		$para = $mail;

		if ($captcha_response and $intruso == "" and $_POST['mail_dos'] == "" and $razon != "" and $mail != "" and $cuil != "") {

// subject
			$titulo = 'Nueva empresa interesada en JobTime';

// message
			$mensajeJob = '
<html>
<head>
  <title>Nueva empresa interesada en JobTime.com.ar</title>
</head>
<body>
  <p>' . $razon . ' quiere que nos pongamos en contacto con ellos, aceptando los terminos y condiciones de jobtime. <br /> Estos son los datos de contacto:!</p>
  <table width="560" border="0">
   <tr>
    <td>Razon Social: ' . $razon . ' </td></tr>
	<tr><td>Email: ' . $mail . '</td></tr>
	<tr><td>CUIL/CUIT: ' . $cuil . ' </td></tr>
	<tr><td>Direccion: ' . $direccion . '</td></tr>
	<tr><td>Nombre de Contacto: ' . $contacto . '</td></tr>
	<tr><td>Dirección: ' . $direccion . ' </td></tr>
	<td>WEB: ' . $web . ' </td>
  </tr>
</table>
</body>
</html>
';

			$mensaje = '
<table width="560" border="0" cellspacing="0" cellpadding="0" >
  <tr>
    <td width="30" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_izq.jpg" /></td>
    <td width="490"><img src="http://www.jobtime.com.ar/images/top_mail_empresa.jpg" /></td>
    <td width="26" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_der.jpg" /></td>
  </tr>
  <tr>
    <td>
    	<p>&nbsp; <strong>BIENVENIDO</strong> ' . $razon . '</p>
        <p>&nbsp; Job Time les brinda una grata Bienvenida!! </p>
		<p>&nbsp; Este equipo quiere agradecerles por establecer contacto</p>
		<p>&nbsp; para formar parte de nuestro staff de Empresas.</p>
		<p>&nbsp; Para poder operar en la Web, nos pondremos en contacto con </p>
		<p>&nbsp; ustedes durante el transcurso del dia para explicarles el funcionamiento de la misma. </p>
	<p>&nbsp; Muchas gracias!!</p>
    <p>&nbsp; El Equipo de JobTime</p>
    </td>
  </tr>
</table>
';


			$subject = "Nueva empresa quiere ser parte de Jobtime.com.ar";

			//mando el correo...

			$from = "info@jobtime.com.ar";

			if (strpos($para, ".ru") === false) {

				mail_php($para, $mensaje, "Jobtime - Nueva Empresa");
				mail_php("info@jobtime.com.ar", $mensajeJob . "ip: " . getRealIP(), "Jobtime - Nueva Empresa");
				//enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Nueva Empresa",$mensaje);

				//enviar_mail(ucwords("jobtime"),$from,$from,"Jobtime - Nueva Empresa",$mensajeJob);
			}

			header("location:index.php?action=alta_empresa&send=1#tab1");
		} else {
			header("Location: ?action=alta_empresa&send=0#tab2");
			exit;
		}

	}
}


function enviar_empresa()
{
global $db,$t;

$razon = $_POST['razon_social'];
$mail = $_POST['email'];
$tel = $_POST['Telefono'];
$cuil = $_POST['cuit'];
$direccion = $_POST['Direccion'];
$nombreContacto = $_POST['nombreContacto'];
$ciudad = $_POST['Ciudad'];
$provincia = $_POST['Provincia'];
$web = $_POST['web'];

$para  = $mail ;

if($razon != "" and $mail != "" and $tel != "" and $cuil != "" and $direccion != "" and $nombreContacto != "" and $ciudad != "" and $provincia != "") 
{

// subject
$titulo = 'Nueva empresa interesada en JobTime';

// message
$mensajeJob = '
<html>
<head>
  <title>Nueva empresa interesada en JobTime.com.ar</title>
</head>
<body>
  <p>'.$razon.' quiere que nos pongamos en contacto con ellos, aceptando los terminos y condiciones de jobtime. <br /> Estos son los datos de contacto:!</p>
  <table width="560" border="0">
   <tr>
    <td>Razon Social: '.$razon.' </td></tr>
	<tr><td>Email: '.$mail.'</td></tr>
	<tr><td>Telefono: '.$tel.'</td></tr>
	<tr><td>CUIL/CUIT: '.$cuil.' </td></tr>
	<tr><td>Direccion: '.$direccion.'</td></tr>
	<tr><td>Nombre de Contacto: '.$nombreContacto.'</td></tr>
	<tr><td>Ciudad: '.$ciudad.' </td></tr>
	<tr><td>Provincia: '.$provincia.'</td></tr>
	<td>WEB: '.$web.' </td>
  </tr>
</table>
</body>
</html>
';

$mensaje = '
<table width="560" border="0" cellspacing="0" cellpadding="0" >
  <tr>
    <td width="30" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_izq.jpg" /></td>
    <td width="490"><img src="http://www.jobtime.com.ar/images/top_mail_empresa.jpg" /></td>
    <td width="26" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_der.jpg" /></td>
  </tr>
  <tr>
    <td>
    	<p>&nbsp; <strong>BIENVENIDO</strong> '.$razon.'</p>
        <p>&nbsp; Job Time les brinda una grata Bienvenida!! </p>
		<p>&nbsp; Este equipo quiere agradecerles por establecer contacto</p>
		<p>&nbsp; para formar parte de nuestro staff de Empresas.</p>
		<p>&nbsp; Para poder operar en la Web, nos pondremos en contacto con </p>
		<p>&nbsp; ustedes durante el transcurso del dia para explicarles el funcionamiento de la misma. </p>
	<p>&nbsp; Muchas gracias!!</p>
    <p>&nbsp; El Equipo de JobTime</p>
    </td>
  </tr>
</table>
';


	$subject = "Nueva empresa quiere ser parte de Jobtime.com.ar";

    //mando el correo...

	$from = "info@jobtime.com.ar";
	
	if ( strpos($para, ".ru") === false)
	{
	mail_php($para,$mensaje,"Jobtime - Nueva Empresa");
	mail_php($from,$mensaje.$mensajeJob."ip: ".getRealIP(),"Jobtime - Nueva Empresa");
	
	//enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Nueva Empresa",$mensaje);
	
	//enviar_mail(ucwords("jobtime"),$from,$from,"Jobtime - Nueva Empresa",$mensajeJob);
	}
 }

	header("location:index.php?action=mostrar_home_page");

}

function cargar_categorias($seleccionada)
{
global $db,$t;

$t->set_block("pl", "listado_categorias","_listado_categorias");

$sql = "SELECT * FROM puestos_trabajo";


$prov = mysqli_query($db,$sql);

while($categorias = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $categorias['numero_puesto'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
			
	$numero_puesto = $categorias['numero_puesto'];
	
	$sql_cantidad = "SELECT COUNT(*) AS cant FROM puestos_trabajo, empleos WHERE puestos_trabajo.numero_puesto = ".$numero_puesto." AND puestos_trabajo.numero_puesto = empleos.puesto_trabajo AND empleos.activo = 1";
	
			
	$cant = mysqli_query($db,$sql_cantidad);
	
	$cantidad_puesto = mysqli_fetch_array($cant);
	
//	echo "cantidad ".$cantidad_puesto['cant']; exit;

	$t->set_var("cantidad",  $cantidad_puesto['cant']);
	$t->set_var("numero_categoria",  $categorias['numero_puesto']);
	$t->set_var("nombre_categoria",  utf8_encode($categorias['nombre_puesto']));
	$t->set_var("nombre_categoria_url",  strtolower(str_replace("/","y",str_replace( " ","-",$categorias['nombre_puesto']))));
	
	$t->set_var("detalle", utf8_encode($categorias['titulo']));
	$t->set_var("empresa", $categorias['razon_social']);
	$t->parse("_listado_categorias", "listado_categorias",true);
}

}

function mapaweb()
{
global $t;

$t->set_file("pl", "mapa.htm");

}
function cargar_documentos($seleccionada)
{
global $db,$t;

$t->set_block("pl", "tipo_documento","_tipo_documento");

$sql = "SELECT * FROM tipo_documento";

$prov = mysqli_query($db,$sql);

if($seleccionada == "")
	$seleccionada = 1;

while($tipo = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $tipo['numero_documento'])
			{			
				$t->set_var("seleccionado", utf8_encode($sel));
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("documento", $tipo['nombre_documento']);
	$t->set_var("num_tipo", $tipo['numero_documento']);
	$t->parse("_tipo_documento", "tipo_documento",true);
}

}


function cargar_paises($seleccionada)
{
global $db,$t;

$t->set_block("pl", "paises","_paises");

$sql = "SELECT * FROM paises";

$prov = mysqli_query($db,$sql);

while($pais = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		{
			if($seleccionada == $pais['numero_pais'])
				{			
					$t->set_var("seleccionado", $sel);
				}	
			else
				$t->set_var("seleccionado", "");
		}		
	else
	{
		$seleccionada = "2";
		if($seleccionada == $pais['numero_pais'])
				{			
					$t->set_var("seleccionado", $sel);
				}	
			else
				$t->set_var("seleccionado", "");
	}
	$t->set_var("pais", utf8_encode($pais['nombre_pais']));
	$t->set_var("num_pais", $pais['numero_pais']);
	$t->parse("_paises", "paises",true);
}

}

function cargar_ramos($seleccionada)
{
global $db,$t;

$t->set_block("pl", "bloque_ramos","_bloque_ramos");

$sql = "SELECT * FROM ramos ORDER BY nombre_ramo";


$prov = mysqli_query($db,$sql);

while($ramo = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
	
		if($seleccionada == $ramo['numero_ramo'])
			{			
				$t->set_var("seleccionado", $sel);
				
			}	
		else
			$t->set_var("seleccionado", "");
			
	$t->set_var("nombre_ramo", $ramo['nombre_ramo']);
	$t->set_var("numero_ramo", $ramo['numero_ramo']);
	$t->parse("_bloque_ramos", "bloque_ramos",true);
}

}


function cargar_estado_civil($seleccionada)
{
global $db,$t;

$t->set_block("pl", "block_estado_civil","_block_estado_civil");

$sql = "SELECT * FROM estado_civiles";


$prov = mysqli_query($db,$sql);

while($civil = mysqli_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $civil['numero_estado_civil'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("estado_civil", $civil['nombre_estado_civil']);
	$t->set_var("num_estado_civil", $civil['numero_estado_civil']);
	$t->parse("_block_estado_civil", "block_estado_civil",true);
}

}


function editarCv()
{
global $db,$t;
	
	$myusername = $_SESSION["usuario"];

	if(!isset($_SESSION["usuario"]))
	{
		header("location:index.php?action=mostrar_home_page&error=usererror");
	}
	
	//$t->set_file("pl", "curriculum-cv-1.htm");
	$t->set_file("pl", "add-resume.html");

	$dni = $_SESSION['usuario'];
	
	$sql = "select * from usuario where dni = '".$dni."'";
	
	$result = mysqli_query($db,$sql); 
	
	$datos_personales = mysqli_fetch_array($result);
	
	$t->set_var("fondo","_form");
	
	$t->set_var("dni", $dni);
	
	$fechaNacimiento = $datos_personales['fechaNacimiento'];
	
	list($anio,$mes,$dia) = explode("-",$fechaNacimiento); 
	

	if(isset($_GET['usuario']))
	{
		cargar_documentos($datos_personales['tipoDocumento']);
		parsear_sexos($datos_personales['sexo']);
		cargar_estado_civil($datos_personales['estadoCivil']);
		cargar_paises($datos_personales['pais']);
		cargar_provincias($datos_personales['provincia']);
		cargar_ciudades($datos_personales['ciudad']);

		$t->set_var("email", $datos_personales['email']);
		$t->set_var("remuneracion", $datos_personales['remuneracion']);
		$t->set_var("nombres", $datos_personales['nombre']);
		$t->set_var("hijos", $datos_personales['hijos']);
		$t->set_var("dia", $dia);
		$t->set_var("mes", $mes);
		$t->set_var("anio", $anio);
		$t->set_var("apellidos", $datos_personales['apellido']);
		$t->set_var("domicilio", $datos_personales['domicilio']);
		$t->set_var("telefono", $datos_personales['telefonoContacto']);
		
	}

}


function contacto()
{
global $db,$t;
	
	$t->set_file("pl", "contact.html");
	if($_GET["mensaje"] == "ok")
		$t->set_var("mensaje", "¡Tu mensaje fue enviado correctamente! en breve respoderemos tu pregunta!");
	elseif($_GET["mensaje"] == "ko")
		$t->set_var("mensaje", "¡Debes indicar que no eres un robot!");




}

function terminios()
{
global $db,$t;
	
	$t->set_file("pl", "terminos.htm");

}

function mail_bienvenida($usuario, $mail)
{
global $db, $t;


// Varios destinatarios
$para  = $mail ;

// subject
$titulo = 'Registro en JobTime';

// message
$mensaje = '
<html>
<head>
  <title>Registro JobTime.com.ar</title>
</head>
<body>
   <table width="560" border="0">
 
  <tr>
    <td><img src="http://www.jobtime.com.ar/images/mail.jpg"  /></td>
  </tr>
  <tr>
    <td><p>Bienvenido <strong>'.$usuario.'</strong></p>
    	<p>Le damos la bienvenida a Jobtime, esperemos cumplir con sus expectativas.....</p><br />
		<br>
		<p>En nuestra pagina publican empleos empresas y consultoras de la región. <br>Para postularte a un empleo, deberás registrarte, cargar tu cv y postularte vía web. 
		<br>Para conocer como funciona nuestra web por favor te pedimos que leas <a href="http:www.jobtime.com.ar/ayuda.pdf">www.jobtime.com.ar/ayuda.pdf</a><br>
Jobtime no realiza entrevistas laborales ni participa de las búsquedas publicadas en nuestro sitio web.</p><br>
		 <p>Gracias por registrarte y confiar en JobTime!</p>
    </td>
  </tr>
   <tr>
    <td><p>Rodrigo Colella | <strong style="color:#009900">CEO</strong><br />WebSite: <a href="http://www.jobtime.com.ar">www.jobtime.com.ar</a><br />Mail: info@jobtime.com.ar</p>
   </td>
  </tr>
</table>
</body>
</html>
';


	$subject = "Nuevo Usuario, Jobtime.com.ar";

    //mando el correo...

	$from = "info@jobtime.com.ar";
	//$headers = "From: $from";
	//$headers = "From: info@jobtime.com.ar\r\nContent-type: text/html\r\n";

	if ( strpos($para, ".ru") === false)
		mail_php($para,$mensaje,"Jobtime - Nuevo Registro");
		//enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Nuevo Registro",$mensaje);
	//enviar_mail(ucwords("jobtime"),$from,$from,"Jobtime - Nuevo Usuario Registrado",$mensaje);
	//mail($para,$subject." Formulario recibido",$mensaje,$headers);
    //mail("rodrigo@jobtime.com.ar",$subject." Formulario recibido",$mensaje,$headers);

}





function registro_paso1()
{
global $db,$t,$path_site;

	if(!isset($_SESSION["usuario"]))
	{
		header("location:index.php?action=mostrar_home_page&error=usererror");
	}
	
	$myusername = $_SESSION["usuario"];

	
	actualizar_dados_paso1($myusername);
	
	//guardo la imagen


	if(isset($_FILES [ 'Ffoto' ]) and $_POST['accion'] != 'guardar_seguir')
	{
		$filename = trim($_FILES['Ffoto']['name']);
		$filename = substr($filename, -20); // tomo las ultimas 20 letras por si el nombre es muy largo
		$filename = str_replace(" ", "", $filename); // quito los espacios

		if( ($filename != "" ) and ( (strpos($filename, ".jpg") !== false) || (strpos($filename, ".JPG") !== false) || (strpos($filename, ".png") !== false) || (strpos($filename, ".PNG") !== false) ) )
		{		
			$imagen=$_FILES [ 'Ffoto' ][ 'tmp_name' ];
			# ruta de la imagen final, si se pone el mismo nombre que la imagen, esta se sobreescribe
			$imagen_final= $path_site."/usuarios/".$_SESSION['usuario']."/";
		
			$tamano = $_FILES["Ffoto"]['size'];
			$tipo = $_FILES["Ffoto"]['type'];
			$archivo = $_FILES["Ffoto"]['name'];
				
			if ($archivo != "") {
			// guardamos el archivo a la carpeta files
			$destino = $imagen_final."avatar/".$archivo;
			if(!file_exists($imagen_final."avatar/"))	
				mkdir($imagen_final."avatar/",  0777);
			if (copy($_FILES['Ffoto']['tmp_name'],$destino)) {
			$status = "Archivo subido: <b>".$archivo."</b>";
			} else {
			$status = "Error al subir el archivo";
			}
			} else {
			$status = "Error al subir archivo";
			}
		
		
			include('resize.php');
			ini_set("memory_limit","90M");
		   $image = new SimpleImage();
		   $image->load($destino);
		   $image->resize(51,55);
		   $image->save($imagen_final.'/avatar.jpg');
		  } 
		else
		{
			echo "Error al cargar imagen, pruebe con una foto sus extenciones jpg o png";
		}
	}
	
	if($_POST['accion'] == 'guardar_seguir')
	{
		$estilo = "mostrar_actualizar_none";
		$t->set_var("mostrar_actualizar",$estilo);
		//ver_paso_2($myusername);
		ver_paso_dos($myusername,$t);
		
	}
		
	elseif($_POST['accion'] == 'guardar_salir')				
				{
					//session_destroy();
					header("location:index.php?action=logearce");
				}
	
}


function registro_paso2()
{
global $db,$t;

	if(!isset($_SESSION["usuario"]))
	{
		header("location:index.php?action=mostrar_home_page&error=usererror");
	}
	
	$myusername = $_SESSION["usuario"];

	$t->set_var("fondo","_form");
	$estilo = "mostrar_actualizar_none";
	$t->set_var("mostrar_actualizar",$estilo);
	
	//actualizar_dados_paso1($myusername);
	if($_POST['accion'] == 'actualizar')
	{
		actualisar_estudio($_POST['numero_estudio']);
		ver_paso_2($myusername);
	}
	elseif($_POST['accion'] == 'guardar_seguir')
	{
		//echo "idEstudio".$_POST['numero_estudio'];exit;
		//var_dump($_POST); exit;
		if($_POST['numero_estudio'] != "")
			actualisar_estudio($_POST['numero_estudio']);
		elseif($_POST['Festudio'] != "")
		{	
			guardar_estudios($myusername);
		}	
		ver_paso3($myusername);
		//cargar_jerarquias("");
	    //cargar_ramos("");
	}	
	elseif($_POST['accion'] == 'seguir')
	{
		
		ver_paso3($myusername);
		
	}	
	else
		ver_paso_2($myusername);
	
	//echo $_POST['accion']; 
	if( isset($_GET['id_estudio']) && !isset($_GET['borrar']) ) // edita un estudio
		{
			$id = $_GET['id_estudio'];
			
			$sql = "SELECT * FROM estudios WHERE id_estudio = ".$id;
		    
			$result = mysqli_query($db,$sql);
			
			$estudios = mysqli_fetch_array($result);
			
			cargar_niveles($estudios['nivel']);
			cargar_provincias($estudios['provincia']);
			cargar_tipos_estudios($_REQUEST['estudio']);
			cargar_estados_estados($_REQUEST['estado']);
			cargar_institucion($estudios['institucion']);
			$t->set_var("otra_inst", $estudios['otra_istitucion']);
			$t->set_var("edit_titulo", $estudios['titulo']);
			$estilo = "mostrar_actualizar'";
			$t->set_var("mostrar_actualizar",$estilo);
			$t->set_var("edit_ingreso", $estudios['anioIngreso']);
			$t->set_var("edit_egreso", $estudios['anioEgreso']);
			$t->set_var("numero_estudio", $estudios['id_estudio']);
			$t->set_var("idioma", $estudios['idioma']);
			$t->set_var("idiomas", $estudios['idiomas']);
			$t->set_var("congresos", $estudios['congresos']);
			$t->set_var("cursos", $estudios['cursos']);

		}	
	elseif(isset($_GET['id_estudio']) && isset($_GET['borrar'])) // borra un estudio 
		{
			$id = $_GET['id_estudio'];
			
			$sql = "DELETE FROM estudios WHERE id_estudio = ".$id;
			
			$result = mysqli_query($db,$sql);
			
			header("location:index.php?action=registro_paso2");
			
		}
	elseif($_POST['accion'] == 'nuevo')
	{
		guardar_estudios($myusername);
		header("location:index.php?action=registro_paso2");
	}
	
	elseif($_POST['accion'] == 'guardar_salir')				
				{
					//session_destroy();
					actualisar_estudio($_GET['id_estudio']);
					header("location:index.php?action=logearce");
				}
	
	
	
}

function registro_paso_dos($t)
{
global $db;

	if(!isset($_SESSION["usuario"]))
	{
		header("location:index.php?action=mostrar_home_page&error=usererror");
	}
	
	$myusername = $_SESSION["usuario"];

	$t->set_var("fondo","_form");
	$estilo = "mostrar_actualizar_none";
	$t->set_var("mostrar_actualizar",$estilo);
	
	ver_paso_dos($myusername,$t);
	
	
}

function ver_nuevo_estudio()
{
global $db,$t;

$t->set_file("pl", "curriculum-cv-dos-nuevo-estudio.htm");
cargar_tipos_estudios("");
cargar_estados_estados("");
cargar_provincias("");
cargar_institucion("");
}

function guardar_nuevo_estudio($db,$t)
{
$festudio= $_POST['Festudio'];
$ftitulo = $_POST['Ftitulo'];
$festado = $_POST['Festado'];
$location1 = $_POST['location1'];
$fingreso = $_POST['Fingreso'];
$fegreso = $_POST['Fegreso'];
$fintitu = $_POST['Finsti'];
$otrainstitu = $_POST['otra_istitucion'];
$dni = $_SESSION["usuario"];


$insert = "INSERT INTO estudios (estudio, titulo, estado, anioIngreso, anioEgreso, institucion, provincia, dni, otra_istitucion,nivel) VALUES (".$festudio.", '".$ftitulo."', ".$festado.", '".$fingreso."', '".$fegreso."', ".$fintitu.", ".$location1.", '".$dni."', '".$otrainstitu."','')";

mysqli_query($db,$insert);

header ("Location: ?action=registro_paso2");
exit;
ver_paso_dos($dni,$t);

}

function ver_paso_dos($usuario,$t)
{
global $db;

//$t->set_file("pl", "curriculum-cv-dos.htm");
$t->set_file("pl", "add-resume_dos.html");
$t->set_block("pl", "estudios","_estudios");
$t->set_block("pl", "cursos","_cursos");
$t->set_block("pl", "idiomas","_idiomas");
$t->set_block("pl", "congresos","_congresos");


$sql = "SELECT * FROM estudios , universidades WHERE estudios.institucion = universidades.numero_universidad AND estudios.dni = ".$usuario;


$result = mysqli_query($db,$sql);

$mis_idiomas ="";
$mis_cursos="";
$mis_congresos="";
while ($estudios = mysqli_fetch_array($result)) 
{
	$sql_tipo_estudio = "SELECT nombre_estudio FROM tipo_estudio WHERE numero_estudio = ". $estudios['estudio'];
		$result_tipo_estudio = mysqli_query($db,$sql_tipo_estudio);
		$sql_tipo_estudio_uno = mysqli_fetch_array($result_tipo_estudio);
	
	$t->set_var("estudio", $sql_tipo_estudio_uno['nombre_estudio']);
	
		$sql_estado = "SELECT nombre_estado FROM estados WHERE numero_estado = ".$estudios['estado'];
		$result_estado = mysqli_query($db,$sql_estado);
		$sql_estado_uno = mysqli_fetch_array($result_estado);
	
	$t->set_var("estado", $sql_estado_uno['nombre_estado'] );
	$t->set_var("ingreso", $estudios['anioIngreso']);
	$t->set_var("titulo", $estudios['titulo']);
	$t->set_var("otra_inst", $estudios['otra_istitucion']);
	$t->set_var("egreso", $estudios['anioEgreso']);
	
		$sql_provincia = "SELECT nombre_provincia FROM provincias WHERE numero_provincia = ".$estudios['provincia'];
		$result_provincia = mysqli_query($db,$sql_provincia);
		$sql_provincia_uno = mysqli_fetch_array($result_provincia);
		
	$t->set_var("provincia", utf8_encode($sql_provincia_uno['nombre_provincia']));
	$t->set_var("institucion", utf8_encode($estudios['nombre_universidad']));
	$t->set_var("id_estudio", $estudios['id_estudio']);
	
	$mi_congreso  = $estudios['congresos'];

	if($mi_congreso != "")
	{
	if($mis_congresos == "")
		$mis_congresos = $mi_congreso;
	else	
	if(!existe_palabra($mis_congresos, $mi_congreso))
		$mis_congresos = $mis_congresos.", ".$mi_congreso;		
	}

	
	$t->set_var("tituloEstudio", "Mis Estudios");
	$t->parse("_estudios", "estudios",true);
	
	
}

	$sql_idiomas = "SELECT * FROM idiomas WHERE dni = ".$usuario;

	$result_idiomas = mysqli_query($db,$sql_idiomas);
	if(mysqli_num_rows($result_idiomas) == 0) $t->set_var("no_tiene_idiomas","style='display:none'");

	while ($lang = mysqli_fetch_array($result_idiomas)) 
	{

		$t->set_var("titulo_idioma",ucfirst($lang['nombre']));
		$t->set_var("nivel_idioma",ucfirst($lang['nivel']));
		$t->set_var("id_idioma",ucfirst($lang['id_idioma']));
		$t->parse("_idiomas", "idiomas",true);
	}


	$sql_cursos = "SELECT * FROM cursos WHERE dni = ".$usuario;
	
	$result_cursos = mysqli_query($db,$sql_cursos);
	if(mysqli_num_rows($result_cursos) == 0) $t->set_var("no_tiene_cursos","style='display:none'");
		
	while ($cur = mysqli_fetch_array($result_cursos)) 
	{

		$titulo_curso = substr ($cur['curso'], 0, 20);
		$t->set_var("titulo_curso",$titulo_curso."..");
		$t->set_var("desc_curso",ucfirst($cur['curso']));
		$t->set_var("id_curso",$cur['id_curso']);
		$t->parse("_cursos", "cursos",true);
	}


	$sql_congresos = "SELECT * FROM congresos WHERE dni = ".$usuario;
	
	$result_congresos = mysqli_query($db,$sql_congresos);
	if(mysqli_num_rows($result_congresos) == 0) $t->set_var("no_tiene_congresos","style='display:none'");

		
	while ($congre = mysqli_fetch_array($result_congresos)) 
	{
		if($congre['congreso'] != "")
		{
			$titulo_congreso = substr ($congre['congreso'], 0, 20);
			$t->set_var("titulo_congreso",$titulo_congreso."..");
			$t->set_var("detalle_congreso",ucfirst($congre['congreso']));
			$t->set_var("id_congreso",$congre['id_congreso']);
			$t->parse("_congresos", "congresos",true);


		}
	}


}

function borrar_curso()
{
	global $db,$t;
	
	$id_curso = $_GET['id_curso'];

	//$sql = "UPDATE estudios SET cursos = '' WHERE id_estudio = ".$Nestudio;

	$myusername = $_SESSION['usuario'];

	$sql = "DELETE FROM cursos WHERE id_curso = ".$id_curso." and dni = ".$myusername;

	mysqli_query($db,$sql);

	header("Location: /?action=registro_paso2");
	exit;
	//ver_paso_dos($myusername,$t);

}

function borrar_estudio()
{ // cuando borro el estudio se me van los cursos cargados
	global $db;
	
	$Nestudio = $_GET['borrar'];

	$myusername = $_SESSION['usuario'];

	$sql = "DELETE FROM estudios WHERE dni = ".$myusername." and id_estudio = ".$Nestudio;
	
	mysqli_query($db,$sql);
	

	header("Location: /?action=registro_paso2");
	exit;
	//ver_paso_dos($myusername,$t);

}

function borrar_idioma()
{
	global $db;
	
	$Nidioma = $_GET['id_idioma'];
	$myusername = $_SESSION['usuario'];

	$sql = "DELETE FROM idiomas WHERE id_idioma = ".$Nidioma." and dni = ".$myusername;

	mysqli_query($db,$sql);

	header("Location: /?action=registro_paso2");
	exit;

}

function update_curso()
{
	global $db,$t;
	
	$Ncurso = $_POST['id_curso'];
	$NewCurso = $_POST['Ftitulo'];
	$myusername = $_SESSION['usuario'];

	$sql = "UPDATE cursos SET curso = '".$NewCurso."' WHERE id_curso = ".$Ncurso." and dni = ".$myusername;
	
	mysqli_query($db,$sql);

	header("Location: /?action=registro_paso2");
	exit;

	//ver_paso_dos($myusername,$t);


}

function actualizar_congreso()
{

	global $db;
	
	$Ncongreso = $_POST['id_congreso'];
	$Newcongreso = $_POST['Ftitulo'];
	$myusername = $_SESSION['usuario'];

	$sql = "UPDATE congresos SET congreso = '".$Newcongreso."' WHERE id_congreso = ".$Ncongreso." and dni = ".$myusername;
	
	mysqli_query($db,$sql);

	header("Location: /?action=registro_paso2");
	exit;

	//ver_paso_dos($myusername,$t);
	
}

function update_idiomas()
{
	global $db,$t;
	
	$Nidioma = $_POST['id_idioma'];
	$Fnivel = $_POST['Fnivel'];
	$NewIdioma = $_POST['Fidioma'];
	$myusername = $_SESSION['usuario'];

	$sql = "UPDATE idiomas SET nombre = '".$NewIdioma."', nivel = '".$Fnivel."' WHERE id_idioma = ".$Nidioma." and dni = ".$myusername;

	mysqli_query($db,$sql);

	header("Location: /?action=registro_paso2");
	exit;
	//ver_paso_dos($myusername,$t);

}


function ver_nuevo_curso()
{
	global $db,$t;
	
	$t->set_file("pl", "curriculum-cv-dos-nuevo-curso.htm");
}

function guardar_nuevo_curso()
{
	global $db;
	
	$dni = $_SESSION['usuario'];
	
	$nuevo_curso = $_POST['curso'];
	
	$consulta = "INSERT INTO cursos (curso, dni) VALUES ('".$nuevo_curso."', '".$dni."')" ;
	
	mysqli_query($db,$consulta);

	header("Location: /?action=registro_paso2");
	exit;
	//ver_paso_dos($dni,$t);
}


function editar_curso($dni,$id_curso)
{
	global $db,$t;
	
	$t->set_file("pl", "curriculum-cv-dos-curso.htm");

	$consulta = "select curso,dni from cursos where dni = ".$_SESSION['usuario']." and curso != '' and id_curso = ".$id_curso;
		
	$result = mysqli_query($db,$consulta);
	
	$num_rows = mysqli_num_rows($result);
		
	if($num_rows == 0)
			echo "<p style='color:red;'><strong>ATENCI&Oacute;N:</strong> No posee cursos cargados!!!<br /><br />Cargue sus cusros con el boton AGREGAR CURSO en la parte superior!</p>";
	else
	{
		$consulta = "select id_curso,curso,dni from cursos where dni = ".$dni." and curso != '' and id_curso = ".$id_curso;
		
		$result = mysqli_query($db,$consulta);
		
		$estudios = mysqli_fetch_array($result);
		 $t->set_var("titulo_curso",$estudios['curso']);
		 $t->set_var("id_curso",$estudios['id_curso']);

	}
}

function ver_nuevo_idioma()
{
global $db,$t;
	
	$t->set_file("pl", "curriculum-cv-dos-nuevo-idioma.htm");
	$t->set_block("pl", "niveles_idiomas","_niveles_idiomas");
	
	
	 cargar_niveles_idiomas('Basico',$t);	
			

}

function guardar_nuevo_idioma()
{
	global $db,$t;
	
	$dni = $_SESSION['usuario'];
	$nombre = $_POST['Fidioma'];
	$nivel = $_POST['Fnivel'];
	
	if($nombre != "")
	{
		$consulta = "INSERT INTO idiomas (dni, nombre, nivel) VALUES ('".$dni."','".$nombre."','".$nivel."')";
		mysqli_query($db,$consulta);
	}

	header("Location: /?action=registro_paso2");
	exit;
	//ver_paso_dos($dni,$t);
}

function editar_idioma($dni,$id_idioma)
{
	global $db,$t;
	
	$t->set_file("pl", "curriculum-cv-dos-idioma.htm");
	$t->set_block("pl", "niveles_idiomas","_niveles_idiomas");

	
	$consulta = "select nombre, nivel, dni, id_idioma from idiomas where dni = ".$dni." and id_idioma = ".$id_idioma;
		
	$result = mysqli_query($db,$consulta);
	
	$num_rows = mysqli_num_rows($result);
		
	if($num_rows == 0)
			echo "<p style='color:red;'><strong>ATENCI&Oacute;N:</strong> No posee idiomas cargados!!!<br /><br />Cargue sus idiomas con el boton AGREGAR IDIOMA en la parte superior!</p>";
	else
	{
	
		$consulta = "select nombre, nivel, dni, id_idioma from idiomas where dni = ".$dni." and id_idioma = ".$id_idioma;
		
		$result = mysqli_query($db,$consulta);
		
		$estudios = mysqli_fetch_array($result);

		 $t->set_var("nombre_idioma",$estudios['nombre']);
		 $t->set_var("id_idioma",$estudios['id_idioma']);
		 cargar_niveles_idiomas($estudios['nivel'],$t);

	}
	
}

function ver_nuevo_congreso()
{
	global $db,$t;
	
	$t->set_file("pl", "curriculum-cv-dos-nuevo-congreso.htm");

}

function guardar_nuevo_congreso()
{
	global $db;

	$congreso = $_POST['Ftitulo'];
	$dni = $_SESSION['usuario'];
	
	if($congreso != "")
	{	
		$consulta = "INSERT INTO congresos (dni, congreso) VALUES ('".$dni."','".$congreso."')";
		mysqli_query($db,$consulta);
	}

	header("Location: /?action=registro_paso2");
	exit;
	//ver_paso_dos($dni,$t);
	
	
}



function editar_congresos($dni,$id_congreso)
{
	global $db,$t;
	
	$t->set_file("pl", "curriculum-cv-dos-congreso.htm");

	$consulta = "select dni, congreso from congresos where dni = ".$_SESSION['usuario']." and congreso != '' and id_congreso = ".$id_congreso;
		
	$result = mysqli_query($db,$consulta);
	
	$num_rows = mysqli_num_rows($result);
		
	if($num_rows == 0)
			echo "<p style='color:red;'><strong>ATENCI&Oacute;N:</strong> No posee congresos cargados!!!<br /><br />Cargue sus congresos con el boton AGREGAR CONGRESO en la parte superior!</p>";
	else
	{
	
		$consulta = "select congreso, dni, id_congreso from congresos where dni = ".$dni." and id_congreso = ".$id_congreso;
		
		$result = mysqli_query($db,$consulta);
		
		$congresos = mysqli_fetch_array($result);

		 if($congresos['congreso'] != "")
		 {
			 $t->set_var("titulo_congreso",$congresos['congreso']);
			 $t->set_var("id_congreso",$congresos['id_congreso']);
		 }

	}
	
}


function borrar_congreso()
{
	global $db;
	
	$id_congreso = $_GET['id_congreso'];
	$dni = $_SESSION['usuario'];
	
	$sql = "DELETE FROM congresos WHERE id_congreso = ".$id_congreso." and dni = ".$dni;
		
	mysqli_query($db,$sql);

	header("Location: /?action=registro_paso2");
	exit;
	//ver_paso_dos($dni,$t);
	
}

function editar_esutio($id_esutio)
{
	global $db,$t;

	$t->set_file("pl", "curriculum-cv-dos-estudio.htm");
	$t->set_block("pl", "estudios","_estudios");
	
	$consulta = "select * from estudios where id_estudio = ".$id_esutio;
	
	$result = mysqli_query($db,$consulta);
	
	$estudios = mysqli_fetch_array($result);
	
	if($estudios['dni'] == $_SESSION['usuario']) // si quiere editar un usuario
		{
		 $t->set_var("edit_titulo",$estudios['titulo']);
		 $t->set_var("edit_ingreso",$estudios['anioIngreso']);
		 $t->set_var("edit_egreso",$estudios['anioEgreso']);
  		 cargar_provincias($estudios['provincia']);
		 cargar_tipos_estudios($estudios['estudio']);
		 cargar_estados_estados($estudios['estado']);
		 cargar_institucion($estudios['institucion']);
		 $t->set_var("otra_inst",$estudios['otra_istitucion']);
		 $t->set_var("numero_estudio",$id_esutio);
		 
		}
	else // si quieren hacer trampa
		{
		echo "intruso!!!";
		die;
		}
}

function existe_palabra($parrafo, $palabra)
{

if(ereg($palabra,$parrafo)) 
	return true;
else
	return false;	
}

function ver_paso_2($usuario)
{
global $db,$t;

$t->set_file("pl", "curriculum-cv-2.htm");
$t->set_block("pl", "estudios","_estudios");

$sql = "SELECT * FROM estudios , universidades WHERE estudios.institucion = universidades.numero_universidad AND estudios.dni = ".$usuario;


$result = mysqli_query($db,$sql);

$mis_idiomas ="";
$mis_cursos="";
$mis_congresos="";
while ($estudios = mysqli_fetch_array($result)) 
{
	$sql_tipo_estudio = "SELECT nombre_estudio FROM tipo_estudio WHERE numero_estudio = ". $estudios['estudio'];
		$result_tipo_estudio = mysqli_query($db,$sql_tipo_estudio);
		$sql_tipo_estudio_uno = mysqli_fetch_array($result_tipo_estudio);
	
	$t->set_var("estudio", $sql_tipo_estudio_uno['nombre_estudio']);
	
		$sql_estado = "SELECT nombre_estado FROM estados WHERE numero_estado = ".$estudios['estado'];
		$result_estado = mysqli_query($db,$sql_estado);
		$sql_estado_uno = mysqli_fetch_array($result_estado);
	
	$t->set_var("estado", $sql_estado_uno['nombre_estado'] );
	$t->set_var("ingreso", $estudios['anioIngreso']);
	$t->set_var("titulo", $estudios['titulo']);
	$t->set_var("otra_inst", $estudios['otra_istitucion']);
	$t->set_var("egreso", $estudios['anioEgreso']);
	
		$sql_provincia = "SELECT nombre_provincia FROM provincias WHERE numero_provincia = ".$estudios['provincia'];
		$result_provincia = mysqli_query($db,$sql_provincia);
		$sql_provincia_uno = mysqli_fetch_array($result_provincia);
		
	$t->set_var("provincia", utf8_encode($sql_provincia_uno['nombre_provincia']));
	$t->set_var("institucion", utf8_encode($estudios['nombre_universidad']));
	$t->set_var("id_estudio", $estudios['id_estudio']);
	
	$mi_idioma =  $estudios['idioma'];
	$idiomas =  $estudios['idiomas'];
	$mi_curso = $estudios['cursos'];
	$mi_congreso  = $estudios['congresos'];
	if($idiomas != "")
	{
		if($idiomas == "")
			$mis_idiomas_dos = $idiomas;	
		else
			if(!existe_palabra($mis_idiomas_dos,$idiomas))
				$mis_idiomas_dos = $mis_idiomas_dos.", ".$idiomas;
	}
	if($mi_idioma != "")
	{
		if($mis_idiomas == "")
			$mis_idiomas = $mi_idioma;	
		else
			if(!existe_palabra($mis_idiomas,$mi_idioma))
				$mis_idiomas = $mis_idiomas.", ".$mi_idioma;
	}
	if($mi_curso != "")
	{
		if($mis_cursos == "")
			$mis_cursos = $mi_curso;
		else	
		if(!existe_palabra($mis_cursos, $mi_curso))
			$mis_cursos = $mis_cursos.", ".$mi_curso;	
	}
	if($mi_congreso != "")
	{
	if($mis_congresos == "")
		$mis_congresos = $mi_congreso;
	else	
	if(!existe_palabra($mis_congresos, $mi_congreso))
		$mis_congresos = $mis_congresos.", ".$mi_congreso;		
	}
	
	// para el color	
	if($cont == 0)
	{
		$t->set_var("color", "_dos");
		$cont = 1;
	}
	else
	{
		$t->set_var("color", "");
		$cont = 0;
	}
	
	$t->set_var("tituloEstudio", "Mis Estudios");
	
	
	$t->parse("_estudios", "estudios",true);
}

if(strlen($mis_idiomas) > 17)
{
	$resto_idioma = substr ($mis_idiomas, 0, 15); 
	$enlace_idiomas = "<a href='#' title='".$mis_idiomas.", ".$mis_idiomas_dos."'>".$resto_idioma."...</a>";	
}
else
	$enlace_idiomas = $mis_idiomas." ".$mis_idiomas_dos;

if(strlen($mis_cursos) > 17)
{
	$resto_curso = substr ($mis_cursos, 0, 15); 
	$enlace_cursos = "<a href='#' title='".$mis_cursos."'>".$resto_curso."...</a>";	
}
else
	$enlace_cursos = $mis_cursos;

if(strlen($mis_congresos) > 17)
{
	$resto_congreso = substr ($mis_congresos, 0, 15); 
	$enlace_congresos = "<a href='#' title='".$mis_congresos."'>".$resto_congreso."...</a>";	
}
else
	$enlace_congresos = $mis_congresos;

$t->set_var("idiomas_parse",$enlace_idiomas);
$t->set_var("cursos_parse",$enlace_cursos);
$t->set_var("congresos_parse",$enlace_congresos);

$t->set_var("color_curso","style='background:#8B98FF'");



if(!isset($_GET['provincia']))
 {
cargar_provincias("");
cargar_tipos_estudios("");
cargar_estados_estados("");
cargar_institucion("");
cargar_niveles("");
}

}




function actualizar_dados_paso1($myusername)
{
global $db;

$nombre = $_POST['Fnombre'];
$apellido = $_POST['Fapellido'];
$tipodni = $_POST['FdniTipo'];
if(isset($_POST['Fnumerodni']))
	$dni = $_POST['Fnumerodni'];
else
	$dni = $myusername;	
$sexo = $_POST["Fsexo"];
$dia_nac = $_POST["day"];
$mes_nac = $_POST["month"];
$remuneracion = $_POST["Fremuneracion"];
$anio_nac = $_POST["year"];
$fechaNacimiento = $anio_nac."-".$mes_nac."-".$dia_nac;
$estado_civil = $_POST["Festadocivil"];
$hijos = $_POST["Fhijos"];
$pais = $_POST["country"];
$provincia = $_POST["location1"];
$ciudad = $_POST["location2"];
$domicilio = $_POST["Fnombre2"];
$email = $_POST["Fnombre3"];
$telefono_contacto = $_POST["Fnombre4"];
$movilidad = $_POST["Fmovilidad"];
$disp_translado = $_POST["Ftraslado"];
$nombre_foto = $_POST["Ffoto"];

$sql = "update usuario set email = '".$email."', nombre = '".$nombre."', remuneracion = '".$remuneracion."', apellido = '".$apellido."', tipoDocumento = '".$tipodni."', sexo = '".$sexo."', fechaNacimiento = '".$fechaNacimiento."', estadoCivil = '".$estado_civil."', hijos = '".$hijos."', pais = '".$pais."', provincia = '".$provincia."', ciudad = '".$ciudad."', domicilio = '".$domicilio."', telefonoContacto = '".$telefono_contacto."', movilidad =  '".$movilidad."', movilidadDisponible = '".$disp_translado."', foto = '".$nombre_foto."' WHERE dni = $dni";


mysqli_query($db,$sql);




}


function ver_paso3($user,$t)
{
global $db;

$usuario = $user;


//$t->set_file("pl", "curriculum-cv-3.htm");
//$t->set_file("pl", "curriculum-cv-tres.htm");
$t->set_file("pl", "add-resume_tres.html");
$t->set_block("pl", "experiencia","_experiencia");

$sql = "SELECT * FROM laboral, jerarquias, ramos  WHERE laboral.dni = ".$usuario." AND laboral.actividad = jerarquias.id_jerarquia AND ramos.numero_ramo = laboral.ramo";

$estilo = "mostrar_actualizar_none";
$t->set_var("mostrar_actualizar",$estilo);

$result = mysqli_query($db,$sql);


while ($experiencias = mysqli_fetch_array($result)) 
{

	$t->set_var("titulo", "Mis Experiencias");	
	$t->set_var("empresa", $experiencias['empresa']);
	$t->set_var("ramo", $experiencias['nombre_ramo'] );
	$t->set_var("desde", $experiencias['desde']);
	$t->set_var("hasta", $experiencias['hasta']);
	$t->set_var("puesto", $experiencias['nombre_jerarquia']);
	$t->set_var("responsabilidades", $experiencias['responsabilidades']);
	$t->set_var("conocimientos", $experiencias['conocimientos']);
	$t->set_var("motivoEgreso", $experiencias['motivoEgreso']);
	$t->set_var("id_laboral", $experiencias['id_laboral']);
	$t->set_var("institucion", $experiencias['nombre_universidad']);
	$t->set_var("id_estudio", $experiencias['id_estudio']);
	$t->parse("_experiencia", "experiencia",true);
}



}


function nueva_experiencia()
{

global $db, $t;

$t->set_file("pl", "curriculum-cv-dos-nueva-experiencia.htm");

$t->set_var("fondo","_form");

cargar_ramos('');
cargar_jerarquias('');

}

function agregar_nueva_experiencia()
{
global $db;

$dni = $_SESSION["usuario"];

$empresa = $_POST["Fempresa"];
$desde = $_POST["Fdesde"];
$hasta = $_POST["Fhasta"]; 
$puesto = $_POST["fpuesto"]; 
$ramo = $_POST["Framo"];
$referencias = $_POST["Freferencias"];
$motivEgreso = $_POST["Fmotivoegreso"];
$reponsabilidades = $_POST["Fresponsabilidaddes"];
$conocimientos = $_POST["Fconocimientos"];

$sql = "INSERT INTO laboral (empresa, desde, hasta, referencias, actividad, responsabilidades, motivoEgreso, ramo, dni, conocimientos) VALUES ('".$empresa."', '".$desde."', '".$hasta."', '".$referencias."', '".$puesto."','".$reponsabilidades."', '".$motivEgreso."', '".$ramo."', '".$dni."' , '".$conocimientos."')";

mysqli_query($db,$sql);
header("Location: index.php?action=ver_paso3");
exit;
 //ver_paso3($dni);
}

function borrar_experiencia()
{
	
global $db;

$dni =  $_SESSION["usuario"];

$id_experiencia = $_GET['id_experiencia'];

$comprobar = "SELECT id_laboral, dni FROM laboral WHERE dni = ". $_SESSION["usuario"]." and id_laboral = ".$id_experiencia;

$result = mysqli_query($db,$comprobar);

$num_rows = mysqli_num_rows($result);

if ($num_rows == 1)
{
	$borrar = "DELETE FROM laboral WHERE dni = ". $_SESSION["usuario"]." and id_laboral = ".$id_experiencia;
	mysqli_query($db,$borrar);
	header("Location: index.php?action=ver_paso3");
	exit;
	//ver_paso3($dni);
}
else
	echo "ACCION NO PERMITIDA!! VUELVA HACIA ATRAS!!";	

}

function actualizar_experiencia($db)
{

$empresa = $_REQUEST["Fempresa"];
$desde = $_REQUEST["Fdesde"];
$hasta = $_REQUEST["Fhasta"]; 
$puesto = $_REQUEST["fpuesto"]; 
$ramo = $_REQUEST["Framo"];
$referencias = $_REQUEST["Freferencias"];
$motivEgreso = $_REQUEST["Fmotivoegreso"];
$responsabilidades = $_REQUEST["Fresponsabilidaddes"];
$conocimientos = $_POST["Fconocimientos"];
$id_experiencia = $_POST["id_experiencia"];

$dni = $_SESSION["usuario"];


$sql = "UPDATE laboral SET empresa = '".$empresa."', desde = '".$desde."', hasta = '".$hasta."', referencias = '".$referencias."', ramo = '".$ramo."',actividad = '".$puesto."', motivoEgreso = '".$motivEgreso."', responsabilidades = '".$responsabilidades."' , conocimientos = '".$conocimientos."' WHERE dni = '".$dni."' and id_laboral = ".$id_experiencia;

mysqli_query($db,$sql);

header("Location: index.php?action=ver_paso3");
exit;
//ver_paso3($dni);
	
}

function registro_paso3()
{

global $db, $t;

$t->set_file("pl", "curriculum-cv-dos-editar-experiencia.htm");

$t->set_var("fondo","_form");

$id = $_GET['id_experiencia'];
			
			
			$sql = "SELECT * FROM laboral WHERE id_laboral = ".$id;
			
			$result = mysqli_query($db,$sql);
			
			$experiencia = mysqli_fetch_array($result);

			cargar_ramos($experiencia['ramo']);
			cargar_jerarquias($experiencia['actividad']);
			
			$estilo = "mostrar_actualizar";
			$t->set_var("mostrar_actualizar",$estilo);
			
			$t->set_var("act_empresa", $experiencia['empresa']);
			$t->set_var("act_desde", $experiencia['desde']);
			$t->set_var("act_hasta", $experiencia['hasta']);
			$t->set_var("act_referencias", $experiencia['referencias']);
			$t->set_var("act_motivo", $experiencia['motivoEgreso']);
			$t->set_var("act_respon", $experiencia['responsabilidades']);
			$t->set_var("conocimientos", $experiencia['conocimientos']);
			$t->set_var("id_experiencia", $experiencia['id_laboral']);
			
	

}

function actualisar_estudio($id_estudio)
{
$idioma = $_POST["Fidioma"];
$idiomas = $_POST["FOidiomas"];
$cursos = $_POST["Fcursos"];
$congresos = $_POST["Fcongresos"];

$estudio_uno = $_POST["Festudio"];
$titulo_uno = $_POST["Ftitulo"];
$estado_uno = $_POST["Festado"]; //estado
$provincia_uno = $_POST["location1"]; // provincia
$ingreso_uno = $_POST["Fingreso"];
$egreso_uno = $_POST["Fegreso"];
$otra_istitucion = $_POST["otra_istitucion"];
$institucion_uno = $_POST["Finsti"];
$nivel_idioma = $_POST['Fnivel'];

$dni = $_SESSION["usuario"];

$sql = "UPDATE estudios SET estudio = '".$estudio_uno."', titulo = '".$titulo_uno."', estado = '".$estado_uno."', anioIngreso = '".$ingreso_uno."', anioEgreso = '".$egreso_uno."', institucion = '".$institucion_uno."', idioma = '".$idioma."', idiomas = '".$idiomas."', cursos = '".$cursos."', congresos = '".$congresos."', provincia = '".$provincia_uno."', otra_istitucion = '".$otra_istitucion."', nivel = '".$nivel_idioma."' WHERE dni = '".$dni."' and id_estudio = ".$id_estudio;


mysqli_query($db,$sql);
}



function guardar_experiencia($dni)
{
global $db;

$empresa = $_POST["Fempresa"];
$desde = $_POST["Fdesde"];
$hasta = $_POST["Fhasta"]; 
$puesto = $_POST["fpuesto"]; 
$ramo = $_POST["Framo"];
$referencias = $_POST["Freferencias"];
$motivEgreso = $_POST["Fmotivoegreso"];
$reponsabilidades = $_POST["Fresponsabilidaddes"];
$conocimientos = $_POST["Fconocimientos"];




$sql = "INSERT INTO laboral (empresa, desde, hasta, referencias, actividad, responsabilidades, motivoEgreso, ramo, dni, conocimientos) VALUES ('".$empresa."', '".$desde."', '".$hasta."', '".$referencias."', '".$puesto."','".$reponsabilidades."', '".$motivEgreso."', '".$ramo."', '".$dni."' , '".$conocimientos."')";


mysqli_query($db,$sql);


}

function guardar_estudios($dni)
{
global $db;

$idioma = $_POST["Fidioma"];
$idiomas = $_POST["FOidiomas"];
$cursos = $_POST["Fcursos"];
$congresos = $_POST["Fcongresos"];

$estudio_uno = $_POST["Festudio"];
$titulo_uno = $_POST["Ftitulo"];
$estado_uno = $_POST["Festado"]; //estado
$provincia_uno = $_POST["location1"]; // provincia
$ingreso_uno = $_POST["Fingreso"];
$egreso_uno = $_POST["Fegreso"];
$institucion_uno = $_POST["Finsti"];
$otra_istitucion = $_POST["otra_istitucion"];
$nivel_idioma = $_POST['Fnivel'];


$sql = "insert into estudios (estudio, titulo, estado, anioIngreso, anioEgreso, institucion, idioma, idiomas, cursos, congresos, dni, provincia, otra_istitucion, nivel) VALUES ('".$estudio_uno."', '".$titulo_uno."', '".$estado_uno."', '".$ingreso_uno."', '".$egreso_uno."','".$institucion_uno."', '".$idioma."', '".$idiomas."', '".$cursos."', '".$congresos."', '".$dni."', '".$provincia_uno."', '".$otra_istitucion."','".$nivel_idioma."')";


mysqli_query($db,$sql);


}


function guardar_paso_3()
{
global $db;

$empresa = $_POST["Fempresa"];
$desde = $_POST["Fdesde"];
$hasta = $_POST["Fhasta"];
$puesto = $_POST["fpuesto"];
$ramo = $_POST["Framo"];
$referencia = $_POST["Freferencias"];
$motivEgreso = $_POST["Fmotivoegreso"];
$responsabilidades = $_POST["Fresponsabilidaddes"];
$conocimientos =  $_POST["Fconocimientos"];

$usuario = $_SESSION['usuario'];

$sql = "INSERT INTO laboral (empresa, desde, hasta, referencias, actividad, ramo,  responsabilidades, motivoEgreso, dni, conocimientos) VALUES ('".$empresa."', '".$desde."', '".$hasta."', '".$referencia."', '".$puesto."', '".$ramo."', '".$responsabilidades."', '".$motivEgreso."' , '".$usuario."' , '".$conocimientos."')";


	mysqli_query($db,$sql);
	
	/*echo "<script type='text/javascript'> alert('Su Curriculum ya ah sido guardado!') </script>";*/
	
	//header("location:index.php?action=ver_paso3");
}


function quienes_somos($t)
{
global $db;
	
	$t->set_file("pl", "quienes_somos.html");
	cargar_ofertas_home_express_new(1);


}

function servicios()
{
global $db,$t;
	

	$t->set_file("pl", "servicios.html");
	cargar_ofertas_home_express_new(1);

}

function aviso_express()
{
global $db,$t;
	
	$t->set_file("pl", "express.html");
	cargar_ofertas_home_express_new(1);


}

function parsear_redes_sociales($usuario)
{
global $db,$t;

$sql = "SELECT facebook,twitter FROM usuario WHERE dni =".$usuario;
$post = mysqli_query($db,$sql);
$datos = mysqli_fetch_array($post);


if($datos['facebook'] == "")
{
	$face = '<a href="#small-dialog" class="popup-with-zoom-anim" style=" display:block; color:#0084b4;" title="Agregar Facebook">Agregar Facebook</a>';
	$t->set_var("boton_facebook_user", "block;");
	$t->set_var("boton_facebook_user_del", "none;");
	$t->set_var("tam_facebook_user", "205px");
	$tiene_linkedinn =  "false";
}
else
{
	$face = $datos['facebook'];
	$t->set_var("tam_twitter_user_del", "block;");
	$t->set_var("boton_facebook_user", "none;");
	$t->set_var("tam_facebook_user", "400px");
	$tiene_linkedinn =  "false";
}
	
if($datos['twitter'] == "")
{
	$twit = '<a href="#small-dialog-linkedin" class="popup-with-zoom-anim" style=" display:block; color:#0084b4;" title="Agregar Linkedin">Agregar Linkedin</a>';
	$t->set_var("tam_twitter_user", "200px");
	$t->set_var("boton_twitter_user_del", "none;");	
	$t->set_var("boton_twitter_user", "block;");
	$tiene_linkedinn =  "false";
}
else
{
	$twit = $datos['twitter'];
	$t->set_var("tam_twitter_user", "520px");
	$t->set_var("boton_twitter_user_del", "block;");	
	$t->set_var("boton_twitter_user", "none;");
	$tiene_linkedinn = "true";
}


$t->set_var("facebook_user", $face);
$t->set_var("twitter_user", $twit);
return $tiene_linkedinn;

}

function add_face()
{
global $db,$t;

$face = $_POST['face'];

$sql = "UPDATE usuario SET facebook = '".$face."' WHERE dni = ".$_SESSION["usuario"];

mysqli_query($db,$sql);

//logearce();
	header("Location: /?action=logearce");
	exit;
}

function borrar_face()
{
global $db,$t;

$sql = "UPDATE usuario SET facebook = '' WHERE dni = ".$_SESSION["usuario"];

mysqli_query($db,$sql);

//logearce();
header("Location: /?action=logearce");
	exit;

}

function borrar_twit()
{
global $db,$t;

$sql = "UPDATE usuario SET twitter = '' WHERE dni = ".$_SESSION["usuario"];

mysqli_query($db,$sql);

//logearce();
	header("Location: /?action=logearce");
	exit;

}

function add_twit()
{
global $db,$t;

$twit = $_POST['twit'];

$sql = "UPDATE usuario SET twitter = '".$twit."' WHERE dni = ".$_SESSION["usuario"];

mysqli_query($db,$sql);

//logearce();
	header("Location: /?action=logearce");
	exit;
}



function parsear_postulaciones($usuario)
{
global $db,$t;

$t->set_block("pl", "mis_postulaciones","_mis_postulaciones");

$sql = "SELECT * FROM postulaciones, empleos WHERE usuario ='".$usuario."' AND empleos.id_empleo = postulaciones.id_empleo ORDER BY postulaciones.fecha DESC";

$postula = mysqli_query($db,$sql);


while($postulaciones = mysqli_fetch_array($postula))
{
	
	$date = date_create($postulaciones['fecha']);
	$fecha_formateada = date_format($date, 'd/m/y');
	$t->set_var("publicacion", $fecha_formateada);
	$t->set_var("titulo", $postulaciones['titulo']);
	$t->set_var("prov", utf8_encode($postulaciones['nombre_provincia']));
	//$t->set_var("loc",  $postulaciones['nombre_ciudad']);
	$t->set_var("loc",  "localidad");
	if( $postulaciones['activo'] == 1)
		$activo = "<span style='font-weight:bold; color:#009933'>Activo</span>";
	else
		$activo = "<span style='font-weight:bold; color:#FF0000'>Caducado</span>";	
	if($postulaciones['interes'] == 1)
	{
		$t->set_var("megusta",'<img src="../images/icon_me_gusta.jpg" alt="CVCompatible" title="Perfil apto por la empresa para este empleo, es posible que se pongan en contacto contigo." style="margin-top:6px;" />');	
	}
	else
		$t->set_var("megusta","");

	if($postulaciones['visto'] == 1)	
		$t->set_var("visto",'<img src="../images/icon_leido.jpg" alt="Leido" title="CV Leido" style="margin-top:6px;" />');
	else	
		$t->set_var("visto",'');
		
	$t->set_var("activo", $activo);
	$t->parse("_mis_postulaciones", "mis_postulaciones",true);
}

}

function parsear_logos_empresas()
{
global $db,$t;

$t->set_block("pl", "logo_empresas","_logo_empresas");

$sql = "SELECT * FROM empresas WHERE razon_social <> 'Importante Empresa' and activa = 1 order by rand()";
$post = mysqli_query($db,$sql);

while($empresas = mysqli_fetch_array($post))
{
	
	$t->set_var("logo", $empresas['logo']);
	$t->set_var("razon_social", $empresas['razon_social']);
	$t->set_var("url", $empresas['logo']);
	$t->parse("_logo_empresas", "logo_empresas",true);
}

}

function parsear_logos_empresas_new($t)
{
global $db,$t;

$t->set_block("pl", "logo_empresas","_logo_empresas");

$sql = "SELECT * FROM empresas WHERE razon_social <> 'Importante Empresa' order by rand()";
$post = mysqli_query($db,$sql);

while($empresas = mysqli_fetch_array($post))
{
	
	$t->set_var("logo", $empresas['logo']);
	$t->set_var("razon_social", $empresas['razon_social']);
	$t->set_var("url", $empresas['logo']);
	$t->parse("_logo_empresas", "logo_empresas",true);
}

}


function parsear_logos_empresas_limit($cant)
{
global $db,$t;

$t->set_block("pl", "logo_empresas","_logo_empresas");

$sql = "SELECT * FROM empresas WHERE razon_social <> 'Importante Empresa' order by rand() LIMIT 0, ".$cant."";

$post = mysqli_query($db,$sql);

while($empresas = mysqli_fetch_array($post))
{
	
	$t->set_var("logo", $empresas['logo']);
	$t->set_var("razon_social", $empresas['razon_social']);
	$t->set_var("url", $empresas['logo']);
	$t->parse("_logo_empresas", "logo_empresas",true);
}

}

function parsear_sexos($seleccionada)
{
global $db,$t;

$t->set_block("pl", "block_sexo","_block_sexo");

$sql = "SELECT * FROM sexos";
$post = mysqli_query($db,$sql);

while($sexo = mysqli_fetch_array($post))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $sexo['numero_sexo'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("numero_sexo", $sexo['numero_sexo']);
	$t->set_var("nombre_sexo", $sexo['nombre_sexo']);

	$t->parse("_block_sexo", "block_sexo",true);
}

}


function entrar()
{
global $db,$t;
	
//	$t->set_file("pl", "usuario.htm");
	
	$usuario = $_POST['usuario'];
	$pass = $_POST['pass'];
	
	if(verificar_usuario($usuario , $pass))
		{
			
			$sql = "select * from usuario where dni = '".$usuario."'";	
			$result = mysqli_query($db,$sql); 
			$tupla = mysqli_fetch_array($result);


			$bienvenido = $tupla['nombre']." ".$tupla['apellido'];
			$localidad = $tupla['ciudad'];
			$apellido = $tupla['apellido'];
			$nombre = $tupla['nombre'];
			$sexo = $tupla['sexo'];
			$provincia = $tupla['provincia'];
			$civil =  $tupla['estadoCivil'];
			$edad = edad($tupla['fechaNacimiento']);

			$t->set_file("pl", "usuario.htm");
			$t->set_var("fondo","_form_user");
			$t->set_var("user", $_SESSION['usuario'] );
			$t->set_var("usuario", $bienvenido);
			parsear_redes_sociales($usuario);
			$t->set_var("apellido", $apellido);
			$t->set_var("nombre", $nombre);
			$t->set_var("dni", $usuario);
			$t->set_var("localidad", $localidad);
			$t->set_var("civil", $civil);
			$t->set_var("edad", $edad);
			parsear_postulaciones($_SESSION['usuario']);
		}
	else
		{
			$t->set_file("pl", "index.htm");
			cargar_areas("");
			cargar_provincias("");
			cargar_ofertas("","");
			$t->set_var("mostrar_error", "_mostrar");	
		}	

}

function usuario_logeado($dni)
{
global $db;

$sql = "select * from user_online where dni = ".$usuario;
$result = mysqli_query($db,$sql); 
$numero = mysqli_num_rows($result);
if($numero == 1)
 return true;
else
 return false; 
}

function desconectar_usuario($dni)
{
global $db;

$sql_online = "update user_online set conectado = 0 where dni = ".$dni;
mysqli_query($db,$sql_online);

}

function logearce()
{
global $db,$t;

	if(isset($_SESSION['usuario']))
		$usuario = $_SESSION['usuario'];
	else	
		$usuario = $_POST['usuario'];
		
	$pass = $_POST['pass'];

	
	if(verificar_usuario($usuario , $pass))
		{
			$sql = "select * from usuario where dni = '".$usuario."'";	
			$result = mysqli_query($db,$sql); 
			$tupla = mysqli_fetch_array($result);


			$bienvenido = $tupla['nombre']." ".$tupla['apellido'];
			$localidad = $tupla['ciudad'];
			$apellido = $tupla['apellido'];
			$email = $tupla['email'];
			$nombre = $tupla['nombre'];
			$provincia = $tupla['provincia'];
			

			$sql = "select * from user_online where dni = ".$usuario;
			$result = mysqli_query($db,$sql); 
			$numero = mysqli_num_rows($result);
			
			$fecha_actual = date("Y-m-d");

			if($numero == 1)
				$sql_online = "update user_online set conectado = 1, ultima_fecha = '".$fecha_actual."' where dni = ".$usuario;
			else
				$sql_online = "insert into user_online (dni, conectado, ultima_fecha) values (".$usuario.",1,".$fecha_actual.")";	
			
			//setcookie("usuario", $usuario, time()+3600,"/","");
			$_COOKIE["usuario"] = $usuario;
			mysqli_query($db,$sql_online);
			$db->set_charset("latin1");


			$t->set_file("pl", "resume-page.html");
			$t->set_block("pl", "habilidades","_habilidades");

			$sql_habilidades = "SELECT * FROM cursos WHERE dni = ".$usuario." limit 5";
			$result_habilidades = mysqli_query($db,$sql_habilidades);
			while($habilidades = mysqli_fetch_array($result_habilidades))
			{
				
				$t->set_var("habilidad", $habilidades['curso']);
				$t->parse("_habilidades", "habilidades",true);
			}

			if($_GET["pass"] == "ok")
				$t->set_var("mensaje_pass", '<div class="skills" style="color:green;">Tu contraseña fue cambiado con EXITO!</div>');


			
			//$t->set_var("fondo","_form_user");
			$t->set_var("usuario", $bienvenido);
			$t->set_var("email", $email);
			$tiene_linkedinn = parsear_redes_sociales($usuario);
			$t->set_var("default", "_default");
			$t->set_var("apellido", $apellido);
			$t->set_var("nombre", $nombre);
			$t->set_var("foto_usuario", $usuario );
			$t->set_var("dni", $usuario);

			$sql_estudio = "select titulo from estudios where dni = ".$usuario;
			$result_estudio = mysqli_query($db,$sql_estudio); 
			$numero_estudio = mysqli_fetch_array($result_estudio);
			$t->set_var("titulo_estudio", $numero_estudio['titulo']);

			if($numero_estudio['titulo'] != "" and $tupla['nombre'] != "" and $tupla['apellido'] != "" and $tupla['ciudad'] != "" and $tupla['apellido'] != "" and $tupla['nombre'] != "" and $tupla['provincia'] != "" and $tiene_linkedinn == "true")
			{
				$t->set_var("puedo_adjuntar", $form_adjuntar_cv);
				$tienecv = "select * from curriculum where usuario = '".$usuario."'";
				$resultcv = mysqli_query($db,$tienecv);
				$result_row = mysqli_fetch_array($resultcv);
				$tienecvadjunto = mysqli_num_rows($resultcv);
				if ($tienecvadjunto == 1)
				{
					$mostrar_attach = "";
					$mensajecv = "display:block;";
					if (strpos($_SERVER["HTTP_HOST"], "localhost") === false)
						$url_cv = "https://www.jobtime.com.ar/usuarios/".$usuario."/avatar/".$result_row["ruta"];
					else
						$url_cv = "http://jobtime.localhost.com.ar/usuarios/".$usuario."/avatar/".$result_row["ruta"];
				}
				else
				{
					$mostrar_attach = "display:block;";
					$mensajecv = "";

				}

				$form_adjuntar_cv = '<form method="post" action="/index.php?action=adjuntar_cv" name="cv_form" onsubmit="return validar_cv();" enctype="multipart/form-data">
        <div style="float:lef; margin-top: 10px;  display:none; '.$mostrar_attach.'" >
        	<p style="color:#616060;">Adjuntar CV: <input type="file" name="cv" id="cv" /> <input type="submit" value="Guardar" /></p>
        </div>
        </form>
        <div style="float:left; display:none; '.$mensajecv.'" >
        	<span style="color:#616060; background:#F9F9F9; padding:3px; border:solid 1px #CCCCCC;"><i class="fa fa-paperclip" aria-hidden="true"></i> Ya tiene un CV Adjunto!  <a title="Borrar CV" href="/?action=borrar_cv"><i class="fa fa-remove"></i></a></span>
        	<span style="color:#616060; background:#F9F9F9; padding:3px; border:solid 1px #CCCCCC;"><a href="'.$url_cv.'" target="_blank"><i class="fa fa-paperclip" aria-hidden="true"></i> Ver CV Adjuntado</a></span>
        </div>';

				$t->set_var("puedo_adjuntar", $form_adjuntar_cv);
			}

			if($provincia != "")
			{
				$sql_prov = "SELECT * FROM provincias WHERE numero_provincia = ".$provincia;
				$prov = mysqli_query($db,$sql_prov);
				$nom_prov = mysqli_fetch_array($prov);
				
				$t->set_var("provincia", $nom_prov['nombre_provincia']);
			}
			else
			{
				$t->set_var("provincia", "");
			}	
			
			if($localidad != "")
			{
				$sql_ciu = "SELECT * FROM ciudades WHERE numero_ciudad = ".$localidad;
				$ciu = mysqli_query($db,$sql_ciu);
				$nom_ciu = mysqli_fetch_array($ciu);
			
				$t->set_var("localidad", utf8_encode($nom_ciu['nombre_ciudad']));
			}
			else
			{
				$t->set_var("localidad","");
			}	
			
			parsear_postulaciones($_SESSION['usuario']);
		}
	else
		{
			header("location: /ingresar_cv");
			exit;
		}	

}

function adjuntar_cv()
{
	global $db,$t,$path_site;

	if($_FILES [ 'cv' ]['name']!= "")
	{
		$filename = trim($_FILES['cv']['name']);
		$filename = substr($filename, -20); // tomo las ultimas 20 letras por si el nombre es muy largo
		$filename = str_replace(" ", "", $filename); // quito los espacios
		$tipo_file = mime_content_types($_FILES['cv']['name']);

		if ( ($tipo_file == "application/pdf") || ($tipo_file == "application/msword") || ($tipo_file == "application/vnd.openxmlformats-officedocument.wordprocessingml.document") )
		{
			$imagen=$_FILES [ 'cv' ][ 'tmp_name' ];
			# ruta de la imagen final, si se pone el mismo nombre que la imagen, esta se sobreescribe
			$imagen_final= $path_site."usuarios/".$_SESSION['usuario']."/";

			$tamano = $_FILES["cv"]['size'];
			$tipo = $_FILES["cv"]['type'];
			$archivo = $_FILES["cv"]['name'];
			$fileNameCmps = explode(".", $archivo);
			$fileExtension = strtolower(end($fileNameCmps));
			$nombre_cv = md5(time() . $archivo) . '.' . $fileExtension;

			if ($nombre_cv != "") {
				// guardamos el archivo a la carpeta files
				$destino = $imagen_final."avatar/".$nombre_cv;

				//if (copy($_FILES['cv']['tmp_name'],$destino))
				if (move_uploaded_file($_FILES['cv']['tmp_name'], $destino))
				{
					$status = "Archivo subido: <b>".$archivo."</b>";
					$sql = "insert into curriculum (usuario,ruta) values ('".$_SESSION['usuario']."','".$nombre_cv."')";

					mysqli_query($db,$sql);


				}
				else {
					$status = "Error al subir el fichero, vuelva atras e intente con otra tipo de fichero!";
					die();
				}
			} else {
				$status = "Error al subir fichero, vuelva atras e intente con otra tipo de fichero!";
				die();
			}
		}
		else
		{
			echo "Error al cargar el CV, pruebe  sus extenciones doc o pdf, vuelva atras e intente con otra tipo de fichero!";
			die();
		}

	}

	header("location:index.php?action=logearce");
	exit;
}

function borrar_cv()
{
	global $db,$t,$path_site;

	$nombre = "select * from curriculum where usuario = '".$_SESSION['usuario']."'";
	$consulta_nombre = mysqli_query($db,$nombre);
	$nombre_fichero = mysqli_fetch_array($consulta_nombre);
	unlink($path_site."/usuarios/".$_SESSION['usuario']."/avatar/".$nombre_fichero['ruta']);

	$sql = "delete from curriculum where usuario = '".$_SESSION['usuario']."'";
	mysqli_query($db,$sql);

	header("location:index.php?action=logearce");

}

function mime_content_types($filename)
{

	$mime_types = array(

		'txt' => 'text/plain',
		'htm' => 'text/html',
		'html' => 'text/html',
		'php' => 'text/html',
		'css' => 'text/css',
		'js' => 'application/javascript',
		'json' => 'application/json',
		'xml' => 'application/xml',
		'swf' => 'application/x-shockwave-flash',
		'flv' => 'video/x-flv',

		// images
		'png' => 'image/png',
		'jpe' => 'image/jpeg',
		'jpeg' => 'image/jpeg',
		'jpg' => 'image/jpeg',
		'gif' => 'image/gif',
		'bmp' => 'image/bmp',
		'ico' => 'image/vnd.microsoft.icon',
		'tiff' => 'image/tiff',
		'tif' => 'image/tiff',
		'svg' => 'image/svg+xml',
		'svgz' => 'image/svg+xml',

		// archives
		'zip' => 'application/zip',
		'rar' => 'application/x-rar-compressed',
		'exe' => 'application/x-msdownload',
		'msi' => 'application/x-msdownload',
		'cab' => 'application/vnd.ms-cab-compressed',

		// audio/video
		'mp3' => 'audio/mpeg',
		'qt' => 'video/quicktime',
		'mov' => 'video/quicktime',

		// adobe
		'pdf' => 'application/pdf',
		'psd' => 'image/vnd.adobe.photoshop',
		'ai' => 'application/postscript',
		'eps' => 'application/postscript',
		'ps' => 'application/postscript',

		// ms office
		'doc' => 'application/msword',
		'rtf' => 'application/rtf',
		'xls' => 'application/vnd.ms-excel',
		'ppt' => 'application/vnd.ms-powerpoint',

		// open office
		'odt' => 'application/vnd.oasis.opendocument.text',
		'ods' => 'application/vnd.oasis.opendocument.spreadsheet',
	);

	$ext = strtolower(array_pop(explode('.',$filename)));
	if (array_key_exists($ext, $mime_types)) {
		return $mime_types[$ext];
	}
	elseif (function_exists('finfo_open')) {
		$finfo = finfo_open(FILEINFO_MIME);
		$mimetype = finfo_file($finfo, $filename);
		finfo_close($finfo);
		return $mimetype;
	}
	else {
		return 'application/octet-stream';
	}

}

function edad($fecha_nac) // calcula la edad de una persona pasando la fecha de nacimiento
{
$dia=date("j");
$mes=date("n");
$anno=date("Y");
$dia_nac=substr($fecha_nac, 8, 2);
$mes_nac=substr($fecha_nac, 5, 2);
$anno_nac=substr($fecha_nac, 0, 4);
if($mes_nac>$mes){
$calc_edad= $anno-$anno_nac-1;
}else{
if($mes==$mes_nac AND $dia_nac>$dia){
$calc_edad= $anno-$anno_nac-1;
}else{
$calc_edad= $anno-$anno_nac;
}
}
return $calc_edad;
}


function verificar_usuario($usuario , $pass)
{
global $db;

	$passw=md5($pass);
	
	if(isset($_SESSION['usuario']) && $_SESSION['usuario'] == $usuario)
		$sql = "select * from usuario where dni = '".$usuario."'";
	else	
		$sql = "select * from usuario where dni = '".$usuario."' and pass = '".$passw."'";
	
	

	$result = mysqli_query($db,$sql); 

	
	$numero = mysqli_num_rows($result);
	
	
	if($numero==1)
		{
			//session_register("usuario");
			$_SESSION['usuario'] = $usuario; 
			return true;
		}
	else
		return false;

}



function logout()
{
global $t;
	
	desconectar_usuario($_SESSION['usuario']);
	$_COOKIE['usuario'] = "";
	unset($_COOKIE['usuario']);

	session_start();
	session_destroy();
	
	$t->set_var("mostrar", "");
	//$t->set_file("pl", "index.htm");
	header("location:/");
	cargar_areas("");
	cargar_provincias("");
	cargar_ofertas("","");
	
}


function ActivarSessionCokie()
{
 if(isset($_SESSION['usuario']))	
 	return true;
else 
{
	$_SESSION['usuario'] = $_COOKIE['usuario'];
	return false;
}
}

function cambiar_imagen()
{
	global $path_site, $t, $db;
	

	$imagen=$_FILES [ 'file' ][ 'tmp_name' ];
	# ruta de la imagen final, si se pone el mismo nombre que la imagen, esta se sobreescribe
	
	$filename = trim($_FILES['file']['tmp_name']);
	$filename = substr($filename, -20); // tomo las ultimas 20 letras por si el nombre es muy largo
	$filename = ereg_replace(" ", "", $filename); // quito los espacios	
	
	if( ($filename != "" ) and ( (ereg(".jpg", $filename)) || (ereg(".JPG", $filename)) || (ereg(".png", $filename)) || (ereg(".PNG", $filename)) ) )
	{
	$imagen_final= $path_site."/usuarios/".$_SESSION['usuario']."/";

	$tamano = $_FILES["file"]['size'];
	$tipo = $_FILES["file"]['type'];
	$archivo = $_FILES["file"]['name'];
		
	if ($archivo != "") {
	// guardamos el archivo a la carpeta files
	$destino = $imagen_final."avatar/".$archivo;
	if(!file_exists($imagen_final."avatar/"))	
		mkdir($imagen_final."avatar/",  0777);
	if (copy($_FILES['file']['tmp_name'],$destino)) {
	$status = "Archivo subido: <b>".$archivo."</b>";
	} else {
	$status = "Error al subir el archivo";
	}
	} else {
	$status = "Error al subir archivo";
	}


 	include('resize.php');
	ini_set("memory_limit","90M");
   $image = new SimpleImage();
   $image->load($destino);
   //$image->resize(51,55);
   $image->save($imagen_final.'/avatar.jpg');

		
	$usuario = $_POST['usuario'];
	}
	else
	{
		echo "error al cargar imagen, las imagenes solo pueden ser jpg o png, vuelva a intentarlo";
	}
	
	logearce();
	
}


function redim($ruta1,$ruta2,$ancho,$alto)
{
# se obtene la dimension y tipo de imagen
$datos=getimagesize ($ruta1);

$ancho_orig = $datos[0]; # Anchura de la imagen original
$alto_orig = $datos[1];    # Altura de la imagen original
$tipo = $datos[2];

if ($tipo==1){ # GIF
if (function_exists("imagecreatefromgif"))
$img = imagecreatefromgif($ruta1);
else
return false;
}
else if ($tipo==2){ # JPG
if (function_exists("imagecreatefromjpeg"))
$img = imagecreatefromjpeg($ruta1);
else
return false;
}
else if ($tipo==3){ # PNG
if (function_exists("imagecreatefrompng"))
$img = imagecreatefrompng($ruta1);
else
return false;
}

# Se calculan las nuevas dimensiones de la imagen
if ($ancho_orig>$alto_orig)
{
$ancho_dest=$ancho;
$alto_dest=($ancho_dest/$ancho_orig)*$alto_orig;
}
else
{
$alto_dest=$alto;
$ancho_dest=($alto_dest/$alto_orig)*$ancho_orig;
}

// imagecreatetruecolor, solo estan en G.D. 2.0.1 con PHP 4.0.6+
$img2=@imagecreatetruecolor($ancho_dest,$alto_dest) or $img2=imagecreate($ancho_dest,$alto_dest);

// Redimensionar
// imagecopyresampled, solo estan en G.D. 2.0.1 con PHP 4.0.6+
@imagecopyresampled($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig) or imagecopyresized($img2,$img,0,0,0,0,$ancho_dest,$alto_dest,$ancho_orig,$alto_orig);


// Crear fichero nuevo, seg�n extensi�n.
if ($tipo==1) // GIF
if (function_exists("imagegif"))
imagegif($img2, $ruta2);
else
return false;

if ($tipo==2) // JPG
if (function_exists("imagejpeg"))
imagejpeg($img2, $ruta2);
else
return false;

if ($tipo==3)  // PNG
if (function_exists("imagepng"))
imagepng($img2, $ruta2);
else
return false;

return true;
}

function enviar_contacto()
{
	if (isset($_POST['g-recaptcha-response'])) {
		$captcha_response = true;
		$recaptcha = $_POST['g-recaptcha-response'];

		$url = 'https://www.google.com/recaptcha/api/siteverify';
		$data = array(
			'secret' => '6LcQ0ZUbAAAAABr-5XAjSI5ZwMwGdWvH-P-Jav6W',
			'response' => $recaptcha
		);
		$options = array(
			'http' => array(
				'method' => 'POST',
				'content' => http_build_query($data)
			)
		);
		$context = stream_context_create($options);
		$verify = file_get_contents($url, false, $context);
		$captcha_success = json_decode($verify);
		$captcha_response = $captcha_success->success;

		if ($captcha_response) {
			$nombre = htmlentities($_POST['userName']);
			$mail = $_POST['userEmail'];
			$consulta = htmlentities($_POST['userComment']);

			$cuerpo = "Formulario enviado\n" . "<br>";
			$cuerpo .= "Nombre: " . $nombre . "\n" . "<br>";
			$cuerpo .= "Email: " . $mail . "\n" . "<br>";
			$cuerpo .= "Consulta: " . $consulta . "\n" . "<br>";


			$subject = "Consulta, Jobtime";

			//mando el correo...

			$from = "info@jobtime.com.ar";
			$headers = "From: $from";

			if (strpos($mail, ".ru") !== false) {
				header("Location: http://www.jobtime.com.ar/index.php?action=mostrar_home_page");
				exit;
			}

			//mail("rodrigo@jobtime.com.ar",$subject." Formulario recibido",$cuerpo,$headers);
			//mail("info@jobtime.com.ar",$subject." Formulario recibido",$cuerpo);

			if ($nombre != "" and $mail != "" and $consulta != "")
				mail_php($from, $cuerpo . "ip: " . getRealIP(), "Jobtime - Consulta Web");
			//enviar_mail(ucwords("jobtime"),"info@jobtime.com.ar","info@jobtime.com.ar","Jobtime - Consulta Web",$cuerpo);

			//doy las gracias por el env�o
			echo "Gracias por rellenar el formulario. Se ha enviado correctamente.";

			header("Location: index.php?action=contacto&mensaje=ok ");
			exit;
		} else {
			header("Location: index.php?action=contacto&mensaje=ko ");
			exit;
		}

	}
	else
		header("Location: index.php?action=contacto&mensaje=ko ");
		exit;
}


function getUsuario($dni)
{
global $db;

$sql = "select * from usuario where dni = ".$dni;

$consulta = mysqli_query($db,$sql);

$ret = mysqli_fetch_array($consulta);

$usuario = $ret['nombre'];

return $usuario;

}

function ver_empleos()
{
global $t, $db;

$t->set_file("pl", "busquedas.htm");
$t->set_var("fondo","_form_empleos");

cargar_provincias("");
cargar_ciudades("");
cargar_areas("");
cargar_jerarquias("");
cargar_ofertas("","");
cargar_categorias("");

$t->set_var("empleo_potulado","/empleo-");
	
	

}

function ver_empleos_new()
{
global $t, $db;

$t->set_file("pl", "browse-jobs.html");

cargar_provincias("");
cargar_ciudades("");
/*
cargar_areas("");
cargar_jerarquias("");
*/
cargar_ofertas("","");
cargar_categorias("");

$t->set_var("empleo_potulado","/empleo-");
	
	

}

function postulado($user, $id_empleo)
{
global $t, $db;

$sql = "SELECT COUNT(*) AS cant FROM postulaciones WHERE usuario = ".$user." and id_empleo = ".$id_empleo;

$prov = mysqli_query($db,$sql);

$postulado = mysqli_fetch_array($prov);

if ($postulado['cant'] > 0)
	return true; // si ya esta postulado
else
	return false;

}

function getIdCiudad($nombre)
{
global $db,$t;
if($nombre != "")
{
$sql = "select * from ciudades where nombre_ciudad = '".$nombre."'";

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$id = $resultado["numero_ciudad"];
}
else
	$id = "";

return $id ;

}

function detalle()
{
global $t, $db;


	$t->set_file("pl", "detalle.htm");
	$t->set_var("fondo", "_form_detalle");

	list($ciudadd, $puestodos) = explode('/', $_GET['ciudad']);
		
	if(isset($_GET['ciudad']))
		if (strpos($_GET['ciudad'], "/") == false) 
			$ciudad = $_GET['ciudad'];
		else
		{
		$ciudadnueva = getIdCiudad($ciudadd);
		$ciudad	= $ciudadnueva;
		}
	
	$usuario = $_SESSION['usuario'];
	
	$no_dejar_postular = 'disabled="disabled"';
	
	if(isset($_SESSION['usuario']))
		$t->set_var("mostrar_mensaje", "style='display:none;'");
	else	
		$t->set_var("mostrar_mensaje", "style='display:block;'"); 
	
	if(isset($_GET['puesto']))
		if($_GET['puesto'] != "")
			$puesto = $_GET['puesto'];
		else
			$puesto = $puestodos;
	
	$sql = "SELECT * FROM empleos, ciudades, empresas, provincias WHERE empleos.ciudad = ciudades.numero_ciudad AND empresas.id_empresa = empleos.id_empresa and empleos.id_empleo = ".$puesto."  GROUP BY empleos.id_empleo";

//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8");

$prov = mysqli_query($db,$sql);

$ofertas = mysqli_fetch_array($prov);

$t->set_var("ciudad",  $ofertas['nombre_ciudad']);
$dealle_ofer =  $ofertas['detalle'];

$dealle_ofer = preg_replace("/<p[^>]*>/","<p>", $dealle_ofer); 
//$dealle_ofer = eregi_replace("<b[^>]*>","<b>", $dealle_ofer); 
//echo $data_item;

if( $ofertas['nuevo'] == 1)
	$t->set_var("detalle", utf8_decode(strip_tags($dealle_ofer, "<br />, <strong>,  <br>, <p>")));
else
	$t->set_var("detalle", utf8_decode(strip_tags($dealle_ofer, "<br />, <strong>,  <br>, <p>")));	
	
$t->set_var("detalle_corto", substr(strip_tags($dealle_ofer), 0, 120));

if($ofertas['activo'])
	$estado = "<span style='font-weight:bold; color:green'>Activo</span>";
else
	$estado = "<span style='font-weight:bold; color:red'>Caducado</span>";	
$t->set_var("estado", $estado);
if($ofertas['nuevo'] == 0)
	$t->set_var("titulo", utf8_decode($ofertas['titulo']));
else
	$t->set_var("titulo", utf8_decode($ofertas['titulo']));
$prov =  getProvincia($ofertas['provincia'], $nombre_prov);
$t->set_var("provincia", $nombre_prov);
if($ofertas['mlogo'] == 1)
	$t->set_var("logo", $ofertas['logo']);
else
	$t->set_var("logo", "importanteempresa");	
$t->set_var("razon_social", $ofertas['razon_social']);
$t->set_var("id_empleo", $ofertas['id_empleo']);


//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$ofertas['disponibilidad'];
	$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
	$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);
	
	$t->set_var("disponibilidad", $nombre_disponibilidad["nombre_disponibilidad"]);
$t->set_var("url", $_SERVER['REQUEST_URI']);



if(isset($_SESSION['usuario']))
	{
	if($_SESSION['usuario'] != "")
		$t->set_var("accion",  "style='display:block;'");
		if(postulado($usuario, $ofertas['id_empleo'])) // si el usuario ya esta postulado no de deja volver a postularte
		{
			$t->set_var("desactivar",  $no_dejar_postular);
			$t->set_var("mensaje",  "Usted ya esta postulado en este empleo!");
		}
	}
else
	$t->set_var("accion",  "style='display:none;'");

if($ofertas['activo'] == 0)
{
	$t->set_var("desactivar",  $no_dejar_postular);
	$t->set_var("mensaje",  "Este empleo se encuentra pausado!");
}

$t->set_var("usuario", $usuario);


}


function detalle_new()
{
global $tn, $db;


	$tn->set_file("pl", "job-page.html");
	$t = $tn;

	list($ciudadd, $puestodos) = explode('/', $_GET['ciudad']);
		
	if(isset($_GET['ciudad']))
		if (strpos($_GET['ciudad'], "/") == false) 
			$ciudad = $_GET['ciudad'];
		else
		{
		$ciudadnueva = getIdCiudad($ciudadd);
		$ciudad	= $ciudadnueva;
		}
	
	$usuario = $_SESSION['usuario'];
	
	$no_dejar_postular = 'disabled="disabled"';
	
	if(isset($_SESSION['usuario']))
		$t->set_var("mostrar_mensaje", "style='display:none;'");
	else	
		$t->set_var("mostrar_mensaje", "style='display:block;'"); 
	
	if(isset($_GET['puesto']))
		if($_GET['puesto'] != "")
			$puesto = $_GET['puesto'];
		else
			$puesto = $puestodos;
	
	$sql = "SELECT * FROM empleos, ciudades, empresas, provincias WHERE empleos.ciudad = ciudades.numero_ciudad AND empresas.id_empresa = empleos.id_empresa and empleos.id_empleo = ".$puesto."  LIMIT 1";

//mysqli_query("SET NAMES 'utf8'");
//$db->set_charset("latin1");
//$db->set_charset("utf8");


$prov = mysqli_query($db,$sql);

$ofertas = mysqli_fetch_array($prov);

$nombre_provincia = get_provincia($db, $ofertas['provinvia']);

$t->set_var("provincia",  $nombre_provincia);
$t->set_var("ciudad",  $ofertas['nombre_ciudad']);
$t->set_var("alta",  date("d-m-Y",strtotime($ofertas['publicacion'])));
$dealle_ofer =  $ofertas['detalle'];

if(mb_detect_encoding($dealle_ofer, "auto") != "UTF-8")
	$dealle_ofer = utf8_encode($dealle_ofer);
else
	{
		$dealle_ofer = $dealle_ofer;
	}


	//$dealle_ofer = eregi_replace("<p[^>]*>","<p>", $dealle_ofer); 
	//$dealle_ofer = preg_replace("<p[^>]*>","<p>", $dealle_ofer); 
	//$dealle_ofer = eregi_replace("<b[^>]*>","<b>", $dealle_ofer); 
	//echo $data_item;
	//$dealle_ofer = preg_replace("/class\s*=\s*'[^\']*[^\']*'/", "", $dealle_ofer);
	$dealle_ofer=preg_replace('/class=".*?"/', '', $dealle_ofer);
	$dealle_ofer=preg_replace('/style=".*?"/', '', $dealle_ofer);
	


	if( $ofertas['nuevo'] == 1)
		$t->set_var("detalle", strip_tags($dealle_ofer, '<br /><strong><br><p>'));
	else
		$t->set_var("detalle", strip_tags($dealle_ofer, '<br /><strong><br><p>'));	



	$t->set_var("detalle_corto", substr(strip_tags($dealle_ofer), 0, 120));

if($ofertas['activo'])
	$estado = "<span style='font-weight:bold; color:green'>Activo</span>";
else
	$estado = "<span style='font-weight:bold; color:red'>Caducado</span>";	
$t->set_var("estado", $estado);
if(mb_detect_encoding($ofertas['titulo'], "auto") != "UTF-8")
	$t->set_var("titulo", utf8_encode($ofertas['titulo']));
else
	$t->set_var("titulo", $ofertas['titulo']);

if($ofertas['mlogo'] == 1) {
	$t->set_var("logo", $ofertas['logo']);
	$t->set_var("razon_social", $ofertas['razon_social']);
}
else {
	$t->set_var("logo", "importanteempresa");
	$t->set_var("razon_social", "Importante Empresa");
}

$t->set_var("id_empleo", $ofertas['id_empleo']);


//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$ofertas['disponibilidad'];
	$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
	$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);
	
	$t->set_var("disponibilidad", $nombre_disponibilidad["nombre_disponibilidad"]);
$t->set_var("url", $_SERVER['REQUEST_URI']);



if(isset($_SESSION['usuario']))
	{
	if($_SESSION['usuario'] != "")
		$t->set_var("accion",  "style='display:block;'");
		if(postulado($usuario, $ofertas['id_empleo'])) // si el usuario ya esta postulado no de deja volver a postularte
		{
			$t->set_var("desactivar",  $no_dejar_postular);
			$t->set_var("mensaje",  "Usted ya esta postulado en este empleo!");
		}
	}
else

	$t->set_var("accion",  "style='display:none;'");

if($ofertas['activo'] == 0)
{
	$t->set_var("desactivar",  $no_dejar_postular);
	$t->set_var("mensaje",  "Este empleo se encuentra pausado!");
}

$t->set_var("usuario", $usuario);


}


function postular()
{
global $t, $db;


	$t->set_file("pl", "postulacion.htm");

	list($ciudadd, $puestodos) = explode('/', $_GET['ciudad']);
		
	if(isset($_GET['ciudad']))
		if (strpos($_GET['ciudad'], "/") == false) 
			$ciudad = $_GET['ciudad'];
		else
		{
		$ciudadnueva = getIdCiudad($ciudadd);
		$ciudad	= $ciudadnueva;
		}
	
	$usuario = $_SESSION['usuario'];
	
	$no_dejar_postular = 'disabled="disabled"';
	
	if(isset($_SESSION['usuario']))
		$t->set_var("mostrar_mensaje", "style='display:none;'");
	else	
		$t->set_var("mostrar_mensaje", "style='display:block;'"); 
	
	if(isset($_GET['puesto']))
		if($_GET['puesto'] != "")
			$puesto = $_GET['puesto'];
		else
			$puesto = $puestodos;
	
	$sql = "SELECT * FROM empleos, ciudades, empresas, provincias WHERE empleos.ciudad = ciudades.numero_ciudad AND empresas.id_empresa = empleos.id_empresa and empleos.id_empleo = ".$puesto."  GROUP BY empleos.id_empleo";

//mysqli_query("SET NAMES 'utf8'");
	$db->set_charset("utf8");

$prov = mysqli_query($db,$sql);

$ofertas = mysqli_fetch_array($prov);

$t->set_var("ciudad",  $ofertas['nombre_ciudad']);
$dealle_ofer =  $ofertas['detalle'];


$t->set_var("detalle", utf8_decode($dealle_ofer));
if($ofertas['activo'])
	$estado = "<span style='font-weight:bold; color:green'>Activo</span>";
else
	$estado = "<span style='font-weight:bold; color:red'>Caducado</span>";	
$t->set_var("estado", $estado);
$t->set_var("titulo", utf8_decode($ofertas['titulo']));
$prov =  getProvincia($ofertas['provincia'], $nombre_prov);
$t->set_var("provincia", $nombre_prov);
if($ofertas['mlogo'] == 1)
	$t->set_var("logo", $ofertas['logo']);
else
	$t->set_var("logo", "importanteempresa");	
$t->set_var("razon_social", $ofertas['razon_social']);
$t->set_var("id_empleo", $ofertas['id_empleo']);


//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$ofertas['disponibilidad'];
	$nom_disponibilidad = mysqli_query($db,$sql_disponibilidad);
	$nombre_disponibilidad = mysqli_fetch_array($nom_disponibilidad);
	
	$t->set_var("disponibilidad", $nombre_disponibilidad["nombre_disponibilidad"]);


if(isset($_SESSION['usuario']))
	{
	if($_SESSION['usuario'] != "")
		$t->set_var("accion",  "style='display:block;'");
		if(postulado($usuario, $ofertas['id_empleo'])) // si el usuario ya esta postulado no de deja volver a postularte
		{
			$t->set_var("desactivar",  $no_dejar_postular);
			$t->set_var("mensaje",  "Usted ya esta postulado en este empleo!");
		}
	}
else

	$t->set_var("accion",  "style='display:none;'");

if($ofertas['activo'] == 0)
	$t->set_var("desactivar",  $no_dejar_postular);

$t->set_var("usuario", $usuario);


}

function enviar_postulacion()
{
global $t, $db;
//var_dump($_POST);

$usuario = $_POST['usuario'];
$empleo = $_POST['id_empleo'];
$fecha_actual  = date("Y-m-d"); 

$ver_mail_empresa = "select e.id_empresa,e.titulo, b.mail from empleos e, empresas b where e.id_empresa = b.id_empresa and e.id_empleo = ".$empleo;
$obtener_mail = mysqli_query($db,$ver_mail_empresa);
$datos_empresa = mysqli_fetch_array($obtener_mail);

$mail_empresa = $datos_empresa['mail'];

$mensaje = '
<table width="560" border="0" cellspacing="0" cellpadding="0" >
  <tr>
    <td width="30" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_izq.jpg" /></td>
    <td width="490"><img src="http://www.jobtime.com.ar/images/top_mail.gif" /></td>
    <td width="26" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_der.jpg" /></td>
  </tr>
  <tr>
    <td>
    	<p>&nbsp; <strong>Jobtime</strong> informa que se a registrado una nueva postulación</p> 
		<p>&nbsp;para el puesto de: '.$datos_empresa['titulo'].'</p>
        <p>&nbsp; Para ver el CV del postulante puede ingresar a su panel administrador. </p>
		<p>&nbsp;Acceso rápido al administrador: <a href="http://www.jobtime.com.ar/empresas/" >AQUí</a></p>
    <p>&nbsp; El Equipo de JobTime</p>
    </td>
  </tr>
</table>
';

/*$from = "info@jobtime.com.ar";

    //mando el correo...
	$posicion = strrpos($datos_empresa['mail'], ",");
	
	if ($posicion == false) {
		
			if($mail_empresa == "josefina.navazo@manpower.com.ar")
				enviar_mail(ucwords("jobtime"),$from,"info@jobtime.com.ar","Jobtime - Nuevo Postulado",$mensaje);
			else	
				enviar_mail(ucwords("jobtime"),$from,$mail_empresa,"Jobtime - Nuevo Postulado",$mensaje);
				
	
	}*/


	
	



$sql = "INSERT INTO postulaciones(id_empleo, usuario, fecha, activo, interes,visto)  VALUES ($empleo, $usuario, '".$fecha_actual."',1,2,0)";

mysqli_query($db,$sql);

//$t->set_file("pl", "usuario.htm");

header("location:index.php?action=logearce");

}

function enviar_amigo()
{

global $db,$t,$template_cv;

$temp = "/mounted-storage/home25/sub032/sc75253-XNXJ/www.jobtime.com.ar/php_templates/cv";
$t = new Template($temp , "remove");
//$t = new Template($template_cv , "remove");

$t->set_file("pl", "mail_recomendar.html");

$usuario = $_POST['usuario_rec'];

$sql = "SELECT * FROM usuario WHERE dni = ".$usuario;

$result = mysqli_query($db,$sql);

$row = mysqli_fetch_array($result);

$amigo = utf8_encode($row['nombre']);

$t->set_var("nombre", $amigo);
$t->set_var("mail", $_POST['emailAmigo']);

$sql_empleo = "select * from empleos where id_empleo = ".$_POST['id_empleo_rec'];

$result_empleo = mysqli_query($db,$sql_empleo);

$datos_empleo = mysqli_fetch_array($result_empleo);

getCiudad($datos_empleo['ciudad'], $ciudad);

$enlace = "buscar-empleo/".$datos_empleo['titulo']; //.$datos_empleo['titulo']."-en-".$ciudad."/".$_POST['id_empleo_rec'];

str_replace(" ","&nbsp;",$enlace); 


$t->set_var("empleo", $datos_empleo['titulo']);

$t->set_var("link", $_SERVER["HTTP_REFERER"]);

$para = $_POST['emailAmigo'];
//$para = "rodrigo@jobtime.com.ar";
//mando el correo...

$from = "info@jobtime.com.ar";
$body = $t->parse("MAIN", "pl");

mail_php($para,$body,"Jobtime - $amigo te recomienda un empleo");
//enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - $amigo te recomienda un empleo",$body);
//enviar_mail(ucwords("jobtime"),$from,"rodrigo@jobtime.com.ar","Jobtime - $amigo te recomienda un empleo",$body);


header("Location: /empleos");

}



function buscar_empleo()
{
global $t, $db;

$t->set_file("pl", "busquedas.htm");
$t->set_var("fondo","_form_empleos");

if(isset($_GET['home']))
{
	$provincia = $_GET['provincia'];
	$area = $_GET['areas'];
	$palabra = $_GET['palabra'];
}
else
{
	if(isset($_GET['empresa']))
	{
		$palabra = $_GET['empresa'];
		$empre = $_GET['empresa'];
	}
	else
		$palabra = $_POST['edit-name'];
	if(isset($_GET['provincia']))
		{
			if($_GET['provincia'] != "")
			$provincia = $_GET['provincia'];
		}
	else	
		$provincia = $_POST['provincia'];
	if(isset($_GET['ciudad']))	
		{
			if($_GET['ciudad'] != "")
			$ciudad = $_GET['ciudad'];
		}
	else
		$ciudad = $_POST['ciudad'];
	
	if(isset($_GET['area']))	
		{
			if($_GET['area'] != "")
			$area = $_GET['area'];
		}
	else
		$area = $_POST['areas'];	
		
	
	$jerarquia = $_POST['jerarquia'];
	$semana = $_POST['fecha'];
}
// para la barra lateral de las busquedas
if(isset($_GET['lateral']))
$area = $_GET['lateral'];
elseif(isset($_POST['lateral']))
$area = $_POST['lateral'];

cargar_provincias($provincia);
cargar_ciudades($ciudad);
cargar_areas($area);
cargar_jerarquias($jerarquia);
cargar_categorias($semana);



$sql = "SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND empleos.activo = 1 and empresas.id_empresa = empleos.id_empresa ";

if($palabra != '')
{
$sql = $sql." AND (empleos.titulo like '%".$palabra."%' OR (empresas.razon_social like '%".$palabra."%' AND empleos.mlogo = 1))";
}
if($provincia != '')
	{
		$sql = $sql." AND provincia = '".$provincia."'";
	}
if(isset($ciudad))
	if($ciudad != '')
	{
		$sql = $sql." AND ciudad = '".$ciudad."'";
	}			
if($area != '')
	{
		if($area != $empre)
			$sql = $sql." AND puesto_trabajo = ".$area;
		
	}
if($jerarquia != '')
	{
		$sql = $sql." AND jerarquia = ".$jerarquia;
	}

cargar_ofertas("",$sql);

$t->set_var("empleo_potulado","/empleo-");

	

}

function buscar_empleo_new()
{
global $t, $db;

$t->set_file("pl", "browse-jobs.html");

if(isset($_GET['home']))
{
	$provincia = $_GET['provincia'];
	$area = $_GET['areas'];
	$palabra = $_GET['palabra'];
}
else
{
	if(isset($_GET['empresa']))
	{
		$palabra = $_GET['empresa'];
		$empre = $_GET['empresa'];
	}
	else
		$palabra = $_POST['edit-name'];
	if(isset($_GET['provincia']))
		{
			if($_GET['provincia'] != "")
			$provincia = $_GET['provincia'];
		}
	else	
		$provincia = $_POST['provincia'];
	if(isset($_GET['ciudad']))	
		{
			if($_GET['ciudad'] != "")
			$ciudad = $_GET['ciudad'];
		}
	else
		$ciudad = $_POST['ciudad'];
	
	if(isset($_GET['area']))	
		{
			if($_GET['area'] != "")
			$area = $_GET['area'];
		}
	else
		$area = $_POST['areas'];	
		
	
	$jerarquia = $_POST['jerarquia'];
	$semana = $_POST['fecha'];
}
// para la barra lateral de las busquedas
if(isset($_GET['lateral']))
$area = $_GET['lateral'];
elseif(isset($_POST['lateral']))
$area = $_POST['lateral'];

$t->set_var("busqueda",$palabra);


cargar_provincias("");
cargar_ciudades("");
//cargar_ofertas("","");
cargar_categorias("");


$sql = "SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND empleos.activo = 1 and empresas.id_empresa = empleos.id_empresa ";

if($palabra != '')
{
$sql = $sql." AND (empleos.titulo like '%".$palabra."%' OR empleos.detalle like '".$palabra."' OR (empresas.razon_social like '%".$palabra."%' AND empleos.mlogo = 1))";
}
if($provincia != '')
	{
		$sql = $sql." AND provincia = '".$provincia."'";
	}
if(isset($ciudad))
	if($ciudad != '')
	{
		$sql = $sql." AND ciudad = '".$ciudad."'";
	}			
if($area != '')
	{
		if($area != $empre)
			$sql = $sql." AND puesto_trabajo = ".$area;
		
	}
if($jerarquia != '')
	{
		$sql = $sql." AND jerarquia = ".$jerarquia;
	}
cargar_ofertas("",$sql);

$t->set_var("empleo_potulado","/empleo-");
	

}

//recuperar pass

function recuperar_pass()
{
global $t;


$t->set_file("pl", "recuperar_contra.htm");


}

function modif_contra()
{

global $db;

$sql = "SELECT * FROM usuario WHERE dni = ".$_SESSION['usuario'];
		
$result = mysqli_query($db,$sql);

$datos_usu = mysqli_fetch_array($result);

$dni = $_SESSION['usuario'];

$email = $datos_usu["email"];

$pass = md5($_POST["contra"]);

$passSin = $_POST["contra"];

$para  = $email ;

$sqlUp = "UPDATE usuario SET pass = '".$pass."' WHERE email = '".$email."' and dni = ".$dni;

mysqli_query($db,$sqlUp);

// subject
$titulo = 'Nueva Contraseña de JobTime';

// message
$mensaje = '
<table width="560" border="0" cellspacing="0" cellpadding="0" >
  <tr>
    <td width="30" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_izq.jpg" /></td>
    <td width="490"><img src="http://www.jobtime.com.ar/images/top_mail.gif" /></td>
    <td width="26" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_der.jpg" /></td>
  </tr>
  <tr>
    <td>
    	<p>&nbsp; <strong>Su nueva contraseña es: </strong> '.$passSin.'</p>
        <p>&nbsp; Job Time le recomienda que la cambie para su seguridad!! </p>
	<p>&nbsp; Muchas gracias!!</p>
    <p>&nbsp; El Equipo de JobTime</p>
    </td>
  </tr>
</table>
';


    //mando el correo... roro 

	$from = "info@jobtime.com.ar";
	//enviar_mail_dos(ucwords("jobtime"),$from,$para,"Jobtime - Nueva Contraseña",$mensaje);
	mail_php($para,$mensaje,"Jobtime - Nueva Contraseña");
	mail_php("rodrigonc_23@hotmail.com",$mensaje,"Jobtime - Nueva Contraseña");
		

	//enviar_mail_dos(ucwords("jobtime"),$from,"rodrigonc_23@hotmail.com","Jobtime - Nueva Contraseña",$mensaje."para el email".$para);
	
	header("Location: /index.php?action=logearce&pass=ok");
	exit;

}


function enviar_pass()
{
global $t,$db;


	$email = trim($_POST["email"]);
	$dni = str_replace(".","",$_POST["dni"]);
	#muestra la cantidad de filas
	
	/*$sql = "SELECT * FROM usuario WHERE email = '".$email."' and dni = ".$dni;

	$resultMail = mysqli_query($db,$sql);
	
	$existe=mysqli_num_rows($resultMail);*/


	$verificar_nombre_sql = "SELECT * FROM usuario WHERE email = ? and dni = ?";
	$stmt = $db->prepare($verificar_nombre_sql);
	$stmt->bind_param("ss", $email,$dni);
	$stmt->execute();
	$result = $stmt->get_result();
	$stmt->close();

	if($result->num_rows > 0)
	{
	
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
	$pass = "";
	for($i=0;$i<12;$i++) {
		$pass .= substr($str,rand(0,62),1);
	}

	$para  = $email ;

	$sqlUp = "UPDATE usuario SET pass = '".md5($pass)."' WHERE email = '".$email."' and dni = ".$dni;

	mysqli_query($db,$sqlUp);

	// subject
	$titulo = 'Nueva Contraseña de JobTime';

	// message
	$mensaje = '
	<table width="560" border="0" cellspacing="0" cellpadding="0" >
	  <tr>
		<td width="30" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_izq.jpg" /></td>
		<td width="490"><img src="http://www.jobtime.com.ar/images/top_mail.gif" /></td>
		<td width="26" rowspan="2"><img src="http://www.jobtime.com.ar/images/plantilla_mail_der.jpg" /></td>
	  </tr>
	  <tr>
		<td>
			<p>&nbsp; <strong>Su nueva contraseña es: </strong> '.$pass.'</p>
			<p>&nbsp; Job Time le recomienda que la cambie para su seguridad!! </p>
		<p>&nbsp; Muchas gracias!!</p>
		<p>&nbsp; El Equipo de JobTime</p>
		</td>
	  </tr>
	</table>
	';


    //mando el correo...

	$from = "info@jobtime.com.ar";
	
	mail_php($para,$mensaje,"Jobtime - Nueva Contraseña");
	mail_php("rodrigonc_23@hotmail.com",$mensaje,"Jobtime - Nueva Contraseña");
	
	//enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Nueva Contraseña",$mensaje);
	
	header("location: http://www.jobtime.com.ar/?action=ingresar_cv&mensaje=enviado#tab1");
}
else
	header("location: http://www.jobtime.com.ar/?action=ingresar_cv&mensaje=noenviado#tab1");

}



?>
