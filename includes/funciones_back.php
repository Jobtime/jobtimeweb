<?php


// *****************************************************************************************************
// Inicio de Funciones Principales
// *****************************************************************************************************

function mostrar_home_page()
{
	global $db,$t;
	
	$t->set_file("pl", "index.htm");
	if(isset($_GET['error']))
		{
			$t->set_var("mostrar_error", "_mostrar");	
		}

	
}

function clientes()
{
	global $db,$t;
	
	$t->set_file("pl", "clientes.htm");
	$t->set_var("fondo","_form_clientes");
	
}


function ingresarCv()
{
global $db,$t;
	
	$t->set_file("pl", "curriculum.htm");

}

function registrarce()
{
global $db,$t;
	
	$nuevo_usuario =  $_POST['new_user'];
	$email = $_POST['email'];
	$pass = md5($_POST['new_password']);
	
	$sql = "select * from usuario where dni = '".$nuevo_usuario."'";
	
	$result = mysql_query($sql); 
	
	$numero = mysql_num_rows($result);

	if($numero == 0 && $nuevo_usuario != "")
		{
			$t->set_file("pl", "curriculum-cv-1.htm");
			$t->set_var("fondo","_form");
			$t->set_var("dni", $nuevo_usuario);
			$t->set_var("email", $email);
			
			$_SESSION['usuario'] = $nuevo_usuario;
			
			$sql = "insert into usuario (dni, pass, email) values ($nuevo_usuario , '".$pass."', '".$email."')";
			
			//echo $sql; exit;
			
			$result = mysql_query($sql);
		}
	else
		{
			$t->set_file("pl", "curriculum.htm");
			$t->set_var("_mostrar", "_mostrar");
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
	
	$t->set_file("pl", "curriculum-cv-1.htm");
	
	$dni = $_SESSION['usuario'];
	
	$sql = "select * from usuario where dni = '".$dni."'";
	
	$result = mysql_query($sql); 
	
	$datos_personales = mysql_fetch_array($result);
	
	$t->set_var("fondo","_form");
	
	$t->set_var("dni", $dni);
	
	if(isset($_GET['usuario']))
	{
		
		$t->set_var("email", $datos_personales['email']);
		$t->set_var("nombres", $datos_personales['nombre']);
		$t->set_var("hijos", $datos_personales['hijos']);
		$t->set_var("apellidos", $datos_personales['apellido']);
		$t->set_var("domicilio", $datos_personales['domicilio']);
		$t->set_var("telefono", $datos_personales['telefonoContacto']);
		
	}

}


function contacto()
{
global $db,$t;
	
	$t->set_var("fondo","_form_mail");
	$t->set_file("pl", "contacto.htm");

}

function terminios()
{
global $db,$t;
	
	$t->set_file("pl", "terminos.htm");

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
	
	actualizar_dados_paso1();
	
	ver_paso_2($myusername);
	
}


function ver_paso_2($usuario)
{
global $db,$t;

$t->set_file("pl", "curriculum-cv-2.htm");

$sql = "SELECT * FROM estudios WHERE dni = ".$usuario;

$result = mysql_query($sql);

$estudios = mysql_fetch_array($result);

$t->set_var("estudio", $estudios['estudio']);
$t->set_var("estado", $estudios['estado']);
$t->set_var("ingreso", $estudios['anioIngreso']);
$t->set_var("titulo", $estudios['titulo']);
$t->set_var("egreso", $estudios['anioEgreso']);
$t->set_var("provincia", $estudios['provincia']);
$t->set_var("institucion", $estudios['institucion']);


}


function actualizar_dados_paso1()
{
global $db;

$nombre = $_POST['Fnombre'];
$apellido = $_POST['Fapellido'];
$tipodni = $_POST['FdniTipo'];
$dni = $_POST['Fnumerodni'];
$sexo = $_POST["Fsexo"];
$dia_nac = $_POST["day"];
$mes_nac = $_POST["month"];
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

$sql = "update usuario set email = '".$email."', nombre = '".$nombre."', apellido = '".$apellido."', tipoDocumento = '".$tipodni."', sexo = '".$sexo."', fechaNacimiento = '".$fechaNacimiento."', estadoCivil = '".$estado_civil."', hijos = '".$hijos."', pais = '".$pais."', provincia = '".$provincia."', ciudad = '".$ciudad."', domicilio = '".$domicilio."', telefonoContacto = '".$telefono_contacto."', movilidad =  '".$movilidad."', movilidadDisponible = '".$disp_translado."', foto = '".$nombre_foto."' WHERE dni = $dni";

mysql_query($sql);

}


function registro_paso3()
{
global $db,$t;

	$myusername = $_SESSION["usuario"];

	if(!isset($_SESSION["usuario"]))
	{
		header("location:index.php?action=mostrar_home_page&error=usererror");
	}
	
	$t->set_var("fondo","_form");
	
	var_dump($_POST); exit;
	
	guardar_estudios($_SESSION["usuario"]);
	
	$t->set_file("pl", "curriculum-cv-3.htm");

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

$estudio_dos = $_POST["Festudio1"];
$titulo_dos = $_POST["Ftitulo1"];
$estado_dos = $_POST["Festado1"];
$localidad_dos = $_POST["location11"];
$ingreso_dos = $_POST["Fingreso1"];
$egreso_dos = $_POST["Fegreso1"];
$institucion_dos = $_POST["Finsti1"];

$estudio_tres = $_POST["Festudio2"];
$titulo_tres = $_POST["Ftitulo2"];
$estado_tres = $_POST["Festado2"];
$localidad_tres = $_POST["location2"];
$ingreso_tres = $_POST["Fingreso2"];
$egreso_tres = $_POST["Fegreso2"];
$institucion_tres = $_POST["Finsti2"]; 

$cant_inserts = $_POST['cant_estudios'];

$sql = "insert into estudios (estudio, titulo, estado, anioIngreso, anioEgreso, institucion, idioma, idiomas, cursos, congresos, dni, provincia) VALUES ('".$estudio_uno."', '".$titulo_uno."', '".$estado_uno."', '".$ingreso_uno."', '".$egreso_uno."','".$institucion_uno."', '".idioma."', '".$idiomas."', '".$cursos."', '".$congresos."', '".$dni."', '".$provincia_uno."')";

$sql_uno = "insert into estudios (estudio, titulo, estado, anioIngreso, anioEgreso, institucion, idioma, idiomas, cursos, congresos, dni, provincia) VALUES ('".$estudio_dos."', '".$titulo_dos."', '".$estado_dos."', '".$ingreso_dos."', '".$egreso_dos."','".$institucion_dos."', '".idioma."', '".$idiomas."', '".$cursos."', '".$congresos."', '".$dni."', '".$provincia_dos."')";

$sql_dos = "insert into estudios (estudio, titulo, estado, anioIngreso, anioEgreso, institucion, idioma, idiomas, cursos, congresos, dni, provincia) VALUES ('".$estudio_tres."', '".$titulo_tres."', '".$estado_tres."', '".$ingreso_tres."', '".$egreso_tres."','".$institucion_tres."', '".idioma."', '".$idiomas."', '".$cursos."', '".$congresos."', '".$dni."', '".$provincia_tres."')";

if($cant_inserts == 1)
{
	mysql_query($sql);
}
else
if($cant_inserts == 2) 
{
	mysql_query($sql);
	mysql_query($sql_uno);
}
else
if($cant_inserts == 3) 
{
	mysql_query($sql);
	mysql_query($sql_uno);
	mysql_query($sql_dos);
}


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

$empresa_uno = $_POST["Fempresa_uno"];
$desde_uno = $_POST["Fdesde_uno"];
$hasta_uno = $_POST["Fhasta_uno"];
$puesto_uno = $_POST["fpuesto_uno"];
$ramo_uno = $_POST["Framo_uno"];
$referencia_uno = $_POST["Freferencias_uno"];
$motivEgreso_uno = $_POST["Fmotivoegreso_uno"];
$responsabilidades_uno = $_POST["Fresponsabilidaddes_uno"];

$empresa_dos = $_POST["Fempresa_dos"];
$desde_dos = $_POST["Fdesde_dos"];
$hasta_dos = $_POST["Fhasta_dos"];
$puesto_dos = $_POST["fpuesto_dos"];
$ramo_dos = $_POST["Framo_dos"];
$referencia_dos = $_POST["Freferencias_dos"];
$motivEgreso_dos = $_POST["Fmotivoegreso_dos"];
$responsabilidades_dos = $_POST["Fresponsabilidaddes_dos"];

$cant_inserts = $_POST["cant_estudios"];

$usuario = $_SESSION['usuario'];

$sql = "INSERT INTO laboral (empresa, desde, hasta, referencias, actividad, ramo,  responsabilidades, motivoEgreso, dni) VALUES ('".$empresa."', '".$desde."', '".$hasta."', '".$referencia."', '".$puesto."', '".$ramo."', '".$responsabilidades."', '".$motivEgreso."' , '".$usuario."')";

$sql_uno = "INSERT INTO laboral (empresa, desde, hasta, referencias, actividad, ramo,  responsabilidades, motivoEgreso, dni) VALUES ('".$empresa_uno."', '".$desde_uno."', '".$hasta_uno."', '".$referencia_uno."', '".$puesto_uno."', '".$ramo_uno."', '".$responsabilidades_uno."', '".$motivEgreso_uno."' , '".$usuario_uno."')";

$sql_dos = "INSERT INTO laboral (empresa, desde, hasta, referencias, actividad, ramo,  responsabilidades, motivoEgreso, dni) VALUES ('".$empresa_dos."', '".$desde_dos."', '".$hasta_dos."', '".$referencia_dos."', '".$puesto_dos."', '".$ramo_dos."', '".$responsabilidades_dos."', '".$motivEgreso_dos."' , '".$usuario_dos."')";


if($cant_inserts == 1)
{
	mysql_query($sql);
}
else
if($cant_inserts == 2) 
{
	mysql_query($sql);
	mysql_query($sql_uno);
}
else
if($cant_inserts == 3) 
{
	mysql_query($sql);
	mysql_query($sql_uno);
	mysql_query($sql_dos);
}

	
	echo "<script type='text/javascript'> alert('Su Curriculum ya ah sido guardado!') </script>";
	
	//header("location:index.php?action=entrar");
}


function quienes_somos()
{
global $db,$t;
	
	$t->set_var("fondo","_form_quienes");
	$t->set_file("pl", "quienes_somos.htm");

}

function servicios()
{
global $db,$t;
	
	$t->set_var("fondo","_form_servicios");
	$t->set_file("pl", "servicios.htm");

}

function entrar()
{
global $db,$t;
	
//	$t->set_file("pl", "usuario.htm");
	
	$usuario = $_POST['usuario'];
	$pass = $_POST['pass'];
	
	if(verificar_usuario($usuario , $pass, $bienvenido, $localidad, $civil, $edad))
		{
			$t->set_file("pl", "usuario.htm");
			$t->set_var("fondo","_form_user");
			$t->set_var("usuario", $bienvenido);
			$t->set_var("dni", $dni);
			$t->set_var("localidad", $localidad);
			$t->set_var("civil", $civil);
			$t->set_var("edad", $edad);
		}
	else
		{
			$t->set_file("pl", "index.htm");
			$t->set_var("mostrar_error", "_mostrar");	
		}	

}


function logearce()
{
global $db,$t;
	
//	$t->set_file("pl", "usuario.htm");
	
	$usuario = $_POST['usuario'];
	$pass = $_POST['pass'];
	
	if(verificar_usuario($usuario , $pass, $bienvenido, $localidad, $civil, $edad))
		{
			$t->set_file("pl", "usuario.htm");
			$t->set_var("fondo","_form_user");
			$t->set_var("usuario", $bienvenido);
			$t->set_var("dni", $dni);
			$t->set_var("localidad", $localidad);
			$t->set_var("civil", $civil);
			$t->set_var("edad", $edad);
		}
	else
		{
			$t->set_file("pl", "curriculum.htm");
			$t->set_var("mostrar_error_log", "_mostrar");	
		}	

}


function verificar_usuario($usuario , $pass, &$bienvenido, &$localidad, &$civil, &$edad)
{
global $db;

	$passw=md5($pass);
	
	$sql = "select * from usuario where dni = '".$usuario."' and pass = '".$passw."'";
	
	$result = mysql_query($sql); 
	
	$numero = mysql_num_rows($result);

	$tupla = mysql_fetch_array($result);
	
	$bienvenido = $tupla['nombre']." ".$tupla['apellido'];
	
	$localidad = $tupla['ciudad'];
	
	$civil =  $tupla['estadoCivil'];
	
	$edad = "28"; //$tupla['edad'];
	
	if($numero==1)
		{
			$_SESSION['usuario'] = $usuario; 
			return true;
		}
	else
		return false;

}



function logout()
{
global $t;
	
	session_start();
	session_destroy();
	
	$t->set_file("pl", "index.htm");
	
}


function redimensionar_jpeg($img_original, $img_nueva, $img_nueva_anchura, $img_nueva_altura, $img_nueva_calidad)
{ 
	// crear una imagen desde el original 
	$img = ImageCreateFromJPEG($img_original); 
	// crear una imagen nueva 
	$thumb = imagecreatetruecolor($img_nueva_anchura,$img_nueva_altura); 
	// redimensiona la imagen original copiandola en la imagen 
	ImageCopyResized($thumb,$img,0,0,0,0,$img_nueva_anchura,$img_nueva_altura,ImageSX($img),ImageSY($img)); 
 	// guardar la nueva imagen redimensionada donde indicia $img_nueva 
	ImageJPEG($thumb,$img_nueva,$img_nueva_calidad);
	ImageDestroy($img);
}


?>
