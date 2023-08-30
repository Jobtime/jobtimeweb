<?php

require_once("funciones_globales.php");

// *****************************************************************************************************
// Inicio de Funciones Principales
// *****************************************************************************************************


function mostrar_home_page()
{
	global $db,$t;
	
	$t->set_file("pl", "index.htm");
	cargar_areas("");
	cargar_provincias("");
	cargar_ofertas("","");
	if(isset($_SESSION['usuario'])) // para abrir los anuncios si estan logeados sino se tiene q logear
		{
		$t->set_var("empleo_potulado","postularme-");
		//$t->set_var("empleo_potulado","/?action=postular");
		$t->set_var("face","rel='facebox'");
		}
	else
		$t->set_var("empleo_potulado","empleo-");
		//$t->set_var("empleo_potulado","/?action=registrarce");

	parsear_logos_empresas();
	if(isset($_GET['error']))
		{
			$t->set_var("mostrar_error", "_mostrar");	
		}

	
}

function ver_cv()
{
global $db,$t;

//$t = new Template($template_cv , "remove");
if($_GET['dni'] != $_SESSION['usuario'])
$t->set_file("pl", "error.htm");
else
{
$t->set_file("pl", "/mounted-storage/home129/sub032/sc75253-XNXJ/www.jobtime.com.ar/php_templates/cv/cv.htm");
$t->set_block("pl", "estudios","_estudios");
$t->set_block("pl", "experiencia","_experiencia");


$usuario = $_GET['dni'];

$sql = "select * from usuario where dni = ".$usuario." ORDER BY apellido";

$sql_estudios = "select * from estudios where dni = ".$usuario;

$sql_laboral = "select * from laboral where dni = ".$usuario;



$resul_laboral = mysql_query($sql_laboral);

$resul_estudio = mysql_query($sql_estudios);

$contadorEstudios=mysql_num_rows($resul_estudio); 

$contadorLaboral=mysql_num_rows($resul_laboral); 

$result=mysql_query($sql);

$number_of_products = mysql_numrows($result);

//seteo los datos del usuario
$datos_usuario = mysql_fetch_array($result);

$t->set_var("apellido",$datos_usuario['apellido']);
$t->set_var("nombre",$datos_usuario['nombre']);
$t->set_var("dni",$datos_usuario['dni']);
$t->set_var("mail",$datos_usuario['email']);
$t->set_var("telefono",$datos_usuario['telefonoContacto']);
$civil = $datos_usuario["estadoCivil"];
	if($civil == 1)
		$estado_civil = "Soltero/a";
	elseif($civil == 2)	
		$estado_civil = "Casado/a";
	elseif($civil == 3)	
		$estado_civil = "Concubinato/a";
	elseif($civil == 4)	
		$estado_civil = "Viudo/a";		
$t->set_var("civil",$estado_civil);
$t->set_var("hijos",$datos_usuario["hijos"]);
$t->set_var("domicilio",$datos_usuario["domicilio"]);
getProvincia($datos_usuario["provincia"],$nombreProv);
$t->set_var("provincia",$nombreProv);
getCiudad($datos_usuario["ciudad"],$nombreCiu);
$t->set_var("ciudad",$nombreCiu);
getPais($datos_usuario["pais"],$nombrePai);
$t->set_var("pais",$nombrePai);
$t->set_var("fecha_nacimiento",$datos_usuario["fechaNacimiento"]);

$idiomasJuntos="";
$idiomatmp="";
$congresosJuntos ="";
//estudios
while($estudios = mysql_fetch_array($resul_estudio))
{

getEstudio($estudios["estudio"], $estudio);
$t->set_var("estudio",$estudio);
$t->set_var("titulo",$estudios["titulo"]);
getEstado($estudios["estado"], $estado);
$t->set_var("estado",$estado);
getInstitucion($estudios["institucion"],$inst);
$t->set_var("institucion",$inst);

$idioma = $estudios["idioma"];
$idiomas = $estudios["idiomas"];
$congresos = $estudios["congresos"];
//idiomas
if($idioma != "")
	if($idiomas == "")
		$idiomasJuntos = $idioma." - ".$idiomasJuntos;
	else
		$idiomasJuntos = $idiomas." - ".$idioma." - ".$idiomasJuntos;
else
	if($idiomas != "")
		$idiomasJuntos = $idiomas." - ".$idiomasJuntos;			
//congresos
if($congresos != "" && $congresos != "Ninguno")
		$congresosJuntos = $congresos." - ".$congresosJuntos;

						
$t->parse("_estudios", "estudios",true);
}
//idiomas y congresos
if($congresosJuntos == "")
	$congresosJuntos = "Ninguno";

$t->set_var("idiomas",$idiomasJuntos);
$t->set_var("congresos",$congresosJuntos);


// experiencias
while($laboral = mysql_fetch_array($resul_laboral))
{

$t->set_var("empresa",$laboral['empresa']);
$t->set_var("desde",$laboral['desde']);
$t->set_var("hasta",$laboral['hasta']);
if($laboral['referencias'] == "")
	$ref = "No especific&oacute;";
else
	$ref =	$laboral['referencias'];
$t->set_var("referencias", $ref);
getActividad($laboral['actividad'],$acti);
$t->set_var("actividad",utf8_encode($acti));
getRamo($laboral['ramo'], $ramoEmpleo);
$t->set_var("rama", utf8_encode($ramoEmpleo));
if($laboral['motivoEgreso'] == "")
	$motivo = "No Especific&oacute;";
else
	$motivo = $laboral['motivoEgreso'];
		
$t->set_var("egreso",$motivo);
$t->set_var("responsabilidades",$laboral['responsabilidades']);
if($laboral['conocimientos'] == "")
	$cono = "No especifi&oacute;";
else
	$cono = $laboral['conocimientos'];	
$t->set_var("conocimientos",$cono);
		
$t->parse("_experiencia", "experiencia",true);
}
}

}


function clientes()
{
	global $db,$t;
	
	$t->set_file("pl", "clientes.htm");
	$t->set_var("fondo","_form_clientes");
	parsear_logos_empresas();
	
}


function ingresarCv()
{
global $db,$t;
	
	$t->set_file("pl", "curriculum.htm");
	parsear_logos_empresas();

}

function registrarce()
{
global $db,$t,$path_site;
	
	$nuevo_usuario =  $_POST['new_user'];
	$email = $_POST['email'];
	$pass = md5($_POST['new_password']);
	
	$sql = "select * from usuario where dni = '".$nuevo_usuario."'";
	
	$fecha_actual  = date("Y-m-d");
	
	$result = mysql_query($sql); 
	
	$numero = mysql_num_rows($result);

	if($numero == 0 && $nuevo_usuario != "")
		{
			$t->set_file("pl", "curriculum-cv-1.htm");
			cargar_provincias($datos_personales['provincia']);
			cargar_ciudades($datos_personales['ciudad']);
			cargar_paises($datos_personales['pais']);
			parsear_sexos("");
			cargar_documentos($datos_personales['tipoDocumento']);
			cargar_estado_civil($datos_personales['estadoCivil']);
			$t->set_var("fondo","_form");
			$t->set_var("dni", $nuevo_usuario);
			$t->set_var("email", $email);
			
			$_SESSION['usuario'] = $nuevo_usuario;
			
			$sql = "insert into usuario (dni, pass, email, alta) values ($nuevo_usuario , '".$pass."', '".$email."', '".$fecha_actual."')";
			
			$directorio = $path_site."/usuarios/".$nuevo_usuario;
			
			//echo $directorio; exit;
			
			mail_bienvenida($nuevo_usuario,$email);
			
			mkdir($directorio);
			
			$result = mysql_query($sql);
		}
	else
		{
			$t->set_file("pl", "curriculum.htm");
			parsear_logos_empresas();
			$mensaje = "ERROR! el usuario ya EXISTE!";
			if( (isset($_GET["puesto"])) && (isset($_GET["ciudad"])) )
				$mensaje = "Debe Registrarse para visualizar los empleos!";
			$t->set_var("mensaje", $mensaje);	
			$t->set_var("_mostrar", "_mostrar");
		}
	

}

function cargar_provincias($seleccionada)
{
global $db,$t;

$t->set_block("pl", "provincias","_provincias");

$sql = "SELECT * FROM provincias";

$prov = mysql_query($sql);

if($seleccionada == "")
	$seleccionada = 107;

while($provincias = mysql_fetch_array($prov))
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

$prov = mysql_query($sql);

while($provincias = mysql_fetch_array($prov))
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

$sql = "SELECT * FROM ciudades";

$prov = mysql_query($sql);

while($provincias = mysql_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $provincias['numero_ciudad'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("ciudad",  utf8_encode($provincias['nombre_ciudad']));
	$t->set_var("num_ciudad", $provincias['numero_ciudad']);
	$t->parse("_ciudades", "ciudades",true);
}

}

function cargar_areas($seleccionada)
{
global $db,$t;

$t->set_block("pl", "areas","_areas");

$sql = "SELECT * FROM areas";

$prov = mysql_query($sql);

while($areas = mysql_fetch_array($prov))
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

$prov = mysql_query($sql);

while($jerarquias = mysql_fetch_array($prov))
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

$niv = mysql_query($sql);

while($niveles = mysql_fetch_array($niv))
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


function cargar_tipos_estudios($seleccionada)
{
global $db,$t;

$t->set_block("pl", "tipo_esutios","_tipo_esutios");

$sql = "SELECT * FROM tipo_estudio";

$prov = mysql_query($sql);

while($estudios = mysql_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $estudios['nombre_estudio'])
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

$prov = mysql_query($sql);

while($estado = mysql_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $estado['nombre_estado'])
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


function cargar_ofertas($seleccionada, $sql_post)
{
global $db,$t;

$importante_empresa = "importanteempresa";

$t->set_block("pl", "listado_ofertas","_listado_ofertas");

if($sql_post == "")
	$sql = "SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND empresas.id_empresa = empleos.id_empresa AND empleos.activo = 1 order by empleos.tipo asc, rand()";
else
	$sql = $sql_post." order by empleos.tipo asc ";	


	//echo $consulta;
	mysql_query("SET NAMES 'utf8'"); 
	$result2=mysql_query($sql); 
	$count = 1;


	$rpp = 10; // cantidad de empleos q quiero ver

	if(!isset($_GET['page'])) 
	{ 
	$pagina=1; 
	$inicio=0; 
	} 
	else 
	{ 
	$pagina=$_GET['page']; 
	$inicio=($pagina-1)*$rpp; 
	} 

	$total_paginas = ceil(mysql_num_rows($result2) / $rpp); 
	$lim_inferior=1; 
	$lim_superior=1; 

//$prov = mysql_query($result2);

while($ofertas = mysql_fetch_array($result2))
{
	if ($lim_inferior>$inicio && $lim_superior<=$rpp)
	{
	$sel = "selected='selected'";
	if($seleccionada != "")
		if($seleccionada == $ofertas['numero_area'])
			{			
				$t->set_var("seleccionado", $sel);
			}	
		else
			$t->set_var("seleccionado", "");
	$t->set_var("ciudad",  $ofertas['nombre_ciudad']);
	
	if($ofertas['mlogo'] == 1)	
		$empresa = "<strong >Empresa: </strong> ".$ofertas['razon_social'];
	else
		$empresa = "<strong >Empresa: </strong> ".$ofertas['asunto'];	
	
	$t->set_var("empresa", $empresa); // si es gold se parsea el nombre de la empresa sino el sector
		
	// busco el nombre del puesto de trabajo
	$sql_p_trabajo = "SELECT * FROM puestos_trabajo WHERE numero_puesto = ".$ofertas['puesto_trabajo'];
	mysql_query("SET NAMES 'utf8'");
	$puesTrabajo = mysql_query($sql_p_trabajo);
	$p_trabajo = mysql_fetch_array($puesTrabajo);
	
	$parse_sector = "<strong> <span style='color:#7e7e7e'>".utf8_decode($ofertas['titulo'])."</span></strong><span style='color:#adb522; font-weight:bold;'>|</span>";
		
	$t->set_var("puesto_trabajo", $p_trabajo['nombre_puesto']);
	$t->set_var("titulo_trabajo", utf8_decode($ofertas['titulo']));

	//$t->set_var("sector", $parse_sector);
	$t->set_var("sector", $parse_sector);
	
	if($ofertas['mlogo'] != 0)
		$t->set_var("logo", $ofertas['logo']);
	else
		$t->set_var("logo", $importante_empresa);
			
	$t->set_var("id_puesto", $ofertas['id_empleo']);
	
	//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$ofertas['disponibilidad'];
	mysql_query("SET NAMES 'utf8'"); 
	$nom_disponibilidad = mysql_query($sql_disponibilidad);
	$nombre_disponibilidad = mysql_fetch_array($nom_disponibilidad);
	
	$t->set_var("dispo", $nombre_disponibilidad["nombre_disponibilidad"]);
	
		$ver_mas = "<a href='{empleo_potulado}". str_replace(" ","-",utf8_decode($ofertas['titulo']))."-en-".str_replace(" ","-",$ofertas['nombre_ciudad'])."/".$ofertas['id_empleo']."' title='Ver Mas' {face}>Ver Mas</a>";
		//$ver_mas = "<a href='{empleo_potulado}&amp;puesto=". $ofertas['id_empleo']."&amp;ciudad=".utf8_encode($ofertas['nombre_ciudad'])."' title='Ver Mas' {face}>Ver Mas</a>";
		
		$enlace = "<a href='{empleo_potulado}". str_replace(" ","-",utf8_decode($ofertas['titulo']))."-en-".str_replace(" ","-",$ofertas['nombre_ciudad'])."/".$ofertas['id_empleo']."' title='Ver Mas' {face}>"; // para el enlace de la home
		//$enlace = "<a href='{empleo_potulado}&amp;puesto=". $ofertas['id_empleo']."&amp;ciudad=".utf8_encode($ofertas['nombre_ciudad'])."' title='Ver Mas' {face}>"; // para el enlace de la home
		
	//tipos de empleos
	if($ofertas['tipo'] == 1)
		{
			$detalle_ofertas = substr($ofertas['detalle'], 0, 120); // parsea el detalle de un empleo
			$t->set_var("tipo_aviso", "oferta_gold");
			$t->set_var("detalle", utf8_decode($detalle_ofertas)."..");
			$t->set_var("ver", $ver_mas);
			$t->set_var("ver_home", $enlace);
		}	
	elseif($ofertas['tipo'] == 2)
		{
			$detalle_ofertas = substr($ofertas['detalle'], 0, 120); // parsea el detalle de un empleo
			$t->set_var("tipo_aviso", "oferta_plata");
			$t->set_var("empresa", $empresa);
			$detail_ofert = utf8_decode($detalle_ofertas).".. ".$ver_mas;
			$t->set_var("detalle", $detail_ofert);
			$t->set_var("ver", "");
			$t->set_var("ver_home", $enlace);
		}	
	elseif($ofertas['tipo'] == 3)
		{
			$detalle_ofertas = substr($ofertas['detalle'], 0, 80); // parsea el detalle de un empleo
			$t->set_var("tipo_aviso", "oferta_bronce");
			$t->set_var("empresa", $parse_sector);	
			$detail_ofert = utf8_decode($detalle_ofertas).".. ".$ver_mas;
			$t->set_var("detalle", $detail_ofert);
			$t->set_var("sector", "");
			$t->set_var("ver", "");
			$t->set_var("ver_home", $enlace);
		}
	else
		{
			$t->set_var("tipo_aviso", "oferta");
			$detalle_ofertas = substr($ofertas['detalle'], 0, 20); // parsea el detalle de un empleo
			$detail_ofert = utf8_decode($detalle_ofertas).".. ".$ver_mas;
			$t->set_var("empresa", $parse_sector." " .$detail_ofert );	
			$t->set_var("detalle", "");
			$t->set_var("sector", "");
			$t->set_var("ver", "");
			$t->set_var("ver_home", $enlace);
		}	
	$t->parse("_listado_ofertas", "listado_ofertas",true);	
	$count++;
	$lim_superior++;
	
	
	}
	
	$lim_inferior++;
	 
}

//PAGINACION
	$paginacion = "";
	//var_dump($_SERVER);
	for ($i=1;$i<=$total_paginas;$i++) 
	{ 
		if ($i!=$pagina) 
		{ 
			if(isset($_GET['action']) )
			{
				if($_GET['action'] == "mostrar_home_page" || $_GET['action'] == "" )
					$hacer = "mostrar_home_page";
				elseif($_GET['action'] == "empleos")
					$hacer = "empleos"	;
			}
			else
				$hacer = "mostrar_home_page";
			if($_GET['action'] == "mostrar_home_page" || $_GET['action'] == "")
				$paginacion .= "<div class='paginacion'><a href='/$i'>$i</a></div> "; 
			elseif($_GET['action'] == "empleos")
				$paginacion .= "<div class='paginacion'><a href='/empleos/$i'>$i</a></div> ";
			else
				$paginacion .= "<div class='paginacion'><a href='/?".$_SERVER["QUERY_STRING"]."&page=$i'>$i</a></div> ";			
		} 
		else 
		{ 
			if($_GET['action'] == "mostrar_home_page" || $_GET['action'] == "")
				$paginacion .= "<div class='selected'>".$i."</div>"; 
			elseif($_GET['action'] == "empleos")	
				$paginacion .= "<div class='selected'><a href='/empleos/$i'>$i</a></div> "; 
			else
				$paginacion .= "<div class='selected'><a href='/?".$_SERVER["QUERY_STRING"]."&page=$i'>$i</a></div> ";	
	
		} 
	} 
	
	$t->set_var("paginacion",$paginacion);
	$t->set_var("ultima_pag",$total_paginas);
	$t->set_var("actual_pag",$pagina);


}

function nueva_empresa()
{
global $db,$t;

$t->set_file("pl", "nueva_empresa.htm");

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
		<p>&nbsp; ustedes para explicarles el funcionamiento de la misma. </p>
	<p>&nbsp; Muchas gracias!!</p>
    <p>&nbsp; El Equipo de JobTime</p>
    </td>
  </tr>
</table>
';


	$subject = "Nueva empresa quiere ser parte de Jobtime.com.ar";

    //mando el correo...

	$from = "info@jobtime.com.ar";
	
	enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Nueva Empresa",$mensaje);
	
	enviar_mail(ucwords("jobtime"),$from,$from,"Jobtime - Nueva Empresa",$mensajeJob);

	header("location:index.php?action=mostrar_home_page");

}

function cargar_categorias($seleccionada)
{
global $db,$t;

$t->set_block("pl", "listado_categorias","_listado_categorias");

$sql = "SELECT * FROM puestos_trabajo";


$prov = mysql_query($sql);

while($categorias = mysql_fetch_array($prov))
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
	
			
	$cant = mysql_query($sql_cantidad);
	
	$cantidad_puesto = mysql_fetch_array($cant);
	
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

$prov = mysql_query($sql);

if($seleccionada == "")
	$seleccionada = 1;

while($tipo = mysql_fetch_array($prov))
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

$prov = mysql_query($sql);

while($pais = mysql_fetch_array($prov))
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


$prov = mysql_query($sql);

while($ramo = mysql_fetch_array($prov))
{
	$sel = "selected='selected'";
	if($seleccionada != "")
	
		if($seleccionada == $ramo['numero_ramo'])
			{			
				$t->set_var("seleccionado", $sel);
				
			}	
		else
			$t->set_var("seleccionado", "");
			
	$t->set_var("nombre_ramo", utf8_encode($ramo['nombre_ramo']));
	$t->set_var("numero_ramo", $ramo['numero_ramo']);
	$t->parse("_bloque_ramos", "bloque_ramos",true);
}

}


function cargar_estado_civil($seleccionada)
{
global $db,$t;

$t->set_block("pl", "block_estado_civil","_block_estado_civil");

$sql = "SELECT * FROM estado_civiles";


$prov = mysql_query($sql);

while($civil = mysql_fetch_array($prov))
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
	
	$t->set_file("pl", "curriculum-cv-1.htm");
	
	$dni = $_SESSION['usuario'];
	
	$sql = "select * from usuario where dni = '".$dni."'";
	
	$result = mysql_query($sql); 
	
	$datos_personales = mysql_fetch_array($result);
	
	$t->set_var("fondo","_form");
	
	$t->set_var("dni", $dni);
	
	$fechaNacimiento = $datos_personales['fechaNacimiento'];
	
	list($anio,$mes,$dia) = split("-",$fechaNacimiento); 
	

	if(isset($_GET['usuario']))
	{
		
		cargar_provincias($datos_personales['provincia']);
		cargar_ciudades($datos_personales['ciudad']);
		cargar_paises($datos_personales['pais']);
		parsear_sexos($datos_personales['sexo']);	
		cargar_documentos($datos_personales['tipoDocumento']);
		cargar_estado_civil($datos_personales['estadoCivil']);
		$t->set_var("email", $datos_personales['email']);
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
	
	$t->set_var("fondo","_form_mail");
	$t->set_file("pl", "contacto.htm");

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
  <p>Gracias por registrarte y confiar en JobTime!</p>
  <table width="560" border="0">
 
  <tr>
    <td><img src="http://www.jobtime.com.ar/images/mail.jpg"  /></td>
  </tr>
  <tr>
    <td><p>Bienvenido <strong>'.$usuario.'</strong></p>
    	<p>Le damos la bienvenida a Jobtime, esperemos cumplir con sus espectativas.....</p><br />
    </td>
  </tr>
   <tr>
    <td><p>Rodrigo Colella <br />WebSite: www.jobtime.com.ar<br />Mail: info@jobtime.com.ar</p>
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

	enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Nuevo Registro",$mensaje);
	enviar_mail(ucwords("jobtime"),$from,$from,"Jobtime - Nuevo Usuario Registrado",$mensaje);
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
	
	$t->set_var("fondo","_form");
	
	actualizar_dados_paso1($myusername);
	
	//guardo la imagen


	if(isset($_FILES [ 'Ffoto' ]))
		if($_FILES [ 'Ffoto' ][ 'tmp_name' ] != "")
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
	
	if($_POST['accion'] == 'guardar_seguir')
	{
		$estilo = "mostrar_actualizar_none";
		$t->set_var("mostrar_actualizar",$estilo);
		ver_paso_2($myusername);
		
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
	else
		ver_paso_2($myusername);
	
	//echo $_POST['accion']; 
	if( isset($_GET['id_estudio']) && !isset($_GET['borrar']) ) // edita un estudio
		{
			$id = $_GET['id_estudio'];
			
			$sql = "SELECT * FROM estudios WHERE id_estudio = ".$id;
		    
			$result = mysql_query($sql);
			
			$estudios = mysql_fetch_array($result);
			
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
			
			$result = mysql_query($sql);
			
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



function ver_paso_2($usuario)
{
global $db,$t;

$t->set_file("pl", "curriculum-cv-2.htm");
$t->set_block("pl", "estudios","_estudios");

$sql = "SELECT * FROM estudios , universidades WHERE estudios.institucion = universidades.numero_universidad AND estudios.dni = ".$usuario;


$result = mysql_query($sql);


while ($estudios = mysql_fetch_array($result)) 
{
	$sql_tipo_estudio = "SELECT nombre_estudio FROM tipo_estudio WHERE numero_estudio = ". $estudios['estudio'];
		$result_tipo_estudio = mysql_query($sql_tipo_estudio);
		$sql_tipo_estudio_uno = mysql_fetch_array($result_tipo_estudio);
	
	$t->set_var("estudio", $sql_tipo_estudio_uno['nombre_estudio']);
	
		$sql_estado = "SELECT nombre_estado FROM estados WHERE numero_estado = ".$estudios['estado'];
		$result_estado = mysql_query($sql_estado);
		$sql_estado_uno = mysql_fetch_array($result_estado);
	
	$t->set_var("estado", $sql_estado_uno['nombre_estado'] );
	$t->set_var("ingreso", $estudios['anioIngreso']);
	$t->set_var("titulo", $estudios['titulo']);
	$t->set_var("otra_inst", $estudios['otra_istitucion']);
	$t->set_var("egreso", $estudios['anioEgreso']);
	
		$sql_provincia = "SELECT nombre_provincia FROM provincias WHERE numero_provincia = ".$estudios['provincia'];
		$result_provincia = mysql_query($sql_provincia);
		$sql_provincia_uno = mysql_fetch_array($result_provincia);
		
	$t->set_var("provincia", utf8_encode($sql_provincia_uno['nombre_provincia']));
	$t->set_var("institucion", utf8_encode($estudios['nombre_universidad']));
	$t->set_var("id_estudio", $estudios['id_estudio']);
	
	
	
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


function ver_paso3($user)
{
global $db,$t;

$usuario = $user;

$t->set_file("pl", "curriculum-cv-3.htm");
$t->set_block("pl", "experiencia","_experiencia");

$sql = "SELECT * FROM laboral, jerarquias, ramos  WHERE laboral.dni = ".$usuario." AND laboral.actividad = jerarquias.id_jerarquia AND ramos.numero_ramo = laboral.ramo";

$estilo = "mostrar_actualizar_none";
	$t->set_var("mostrar_actualizar",$estilo);

$result = mysql_query($sql);

$cont = 0;

while ($experiencias = mysql_fetch_array($result)) 
{
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
	$t->set_var("titulo", "Mis Experiencias");	
	$t->set_var("empresa", $experiencias['empresa']);
	$t->set_var("ramo", utf8_encode($experiencias['nombre_ramo']) );
	$t->set_var("desde", $experiencias['desde']);
	$t->set_var("hasta", $experiencias['hasta']);
	$t->set_var("puesto", utf8_encode($experiencias['nombre_jerarquia']));
	$t->set_var("id_laboral", $experiencias['id_laboral']);
	$t->set_var("institucion", $experiencias['nombre_universidad']);
	$t->set_var("id_estudio", $experiencias['id_estudio']);
	$t->parse("_experiencia", "experiencia",true);
}
if($_GET['accion'] == "" && !isset($_GET['id_experiencia']))
{
cargar_ramos("");
cargar_jerarquias("");
$t->set_var("fondo","_form");
}
elseif(!isset($_GET['id_experiencia']))
{
cargar_ramos("");
cargar_jerarquias("");
$t->set_var("fondo","_form");
}

}



function registro_paso3()
{

global $db, $t;

if(!isset($_SESSION["usuario"]))
	{
		header("location:index.php?action=mostrar_home_page&error=usererror");
	}
	
	$myusername = $_SESSION["usuario"];
	
	$t->set_var("fondo","_form");
	
	
	
	if( isset($_GET['id_experiencia']) && !isset($_GET['borrar']) ) // edita una experiencia
		{
			$id = $_GET['id_experiencia'];
			
			$t->set_var("id_experiencia", $id);
			
			$sql = "SELECT * FROM laboral WHERE id_laboral = ".$id;
			
			//echo $sql; exit;
		    
			$result = mysql_query($sql);
			
			$experiencia = mysql_fetch_array($result);
			
			ver_paso3($myusername);
			
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

		}	
	elseif(isset($_GET['id_experiencia']) && isset($_GET['borrar'])) // borra un experiencia 
		{
			$id = $_GET['id_experiencia'];
			
			$t->set_var("id_experiencia", $id);
			
			$sql = "DELETE FROM laboral WHERE id_laboral = ".$id;
			
			$result = mysql_query($sql);
			
			header("location:index.php?action=ver_paso3");
			
		}	
	elseif($_POST['accion'] == 'actualizar')	
	{
		actualisar_experiencia($_POST['id_experiencia']);
		ver_paso3($myusername);
	}
	elseif($_POST['accion'] == 'guardar_salir')				
				{
					actualisar_experiencia($_POST['id_experiencia']);
					header("location:index.php?action=logearce");
				}
		elseif($_POST['accion'] == 'guardar')
			{		
					actualisar_experiencia($_POST['id_experiencia']);
					header("location:index.php?action=logearce");
			}		
			elseif($_POST['accion'] == 'nuevo')
			{
					guardar_experiencia($myusername);
					ver_paso3($myusername);
			}
			elseif($_POST['accion'] == '')
				{
				cargar_ramos("");
			
				cargar_jerarquias("");
				}	

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


mysql_query($sql);
}

function actualisar_experiencia($id_experiencia)
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

$dni = $_SESSION["usuario"];


$sql = "UPDATE laboral SET empresa = '".$empresa."', desde = '".$desde."', hasta = '".$hasta."', referencias = '".$referencias."', ramo = '".$ramo."',actividad = '".$puesto."', motivoEgreso = '".$motivEgreso."', responsabilidades = '".$responsabilidades."' , conocimientos = '".$conocimientos."' WHERE dni = '".$dni."' and id_laboral = ".$id_experiencia;


mysql_query($sql);
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


mysql_query($sql);


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


mysql_query($sql);


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


	mysql_query($sql);
	
	/*echo "<script type='text/javascript'> alert('Su Curriculum ya ah sido guardado!') </script>";*/
	
	//header("location:index.php?action=ver_paso3");
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


function parsear_postulaciones($usuario)
{
global $db,$t;

$t->set_block("pl", "mis_postulaciones","_mis_postulaciones");

$sql = "SELECT * FROM postulaciones, empleos, provincias, ciudades WHERE usuario =".$usuario." AND empleos.id_empleo = postulaciones.id_empleo AND ciudades.numero_ciudad = empleos.ciudad AND provincias.numero_provincia = empleos.provincia ORDER BY postulaciones.fecha";
$post = mysql_query($sql);

while($postulaciones = mysql_fetch_array($post))
{
	
	$t->set_var("publicacion", $postulaciones['fecha']);
	$t->set_var("titulo", $postulaciones['titulo']);
	$t->set_var("prov", utf8_decode($postulaciones['nombre_provincia']));
	$t->set_var("loc",  utf8_decode($postulaciones['nombre_ciudad']));
	if( $postulaciones['activo'] == 1)
		$activo = "<span style='font-weight:bold; color:#009933'>Activo</span>";
	else
		$activo = "<span style='font-weight:bold; color:#FF0000'>Caducado</span>";	
	$t->set_var("activo", $activo);
	$t->parse("_mis_postulaciones", "mis_postulaciones",true);
}

}

function parsear_logos_empresas()
{
global $db,$t;

$t->set_block("pl", "logo_empresas","_logo_empresas");

$sql = "SELECT * FROM empresas WHERE razon_social <> 'Importante Empresa' order by rand()";
$post = mysql_query($sql);

while($empresas = mysql_fetch_array($post))
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
$post = mysql_query($sql);

while($sexo = mysql_fetch_array($post))
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
	
	if(verificar_usuario($usuario , $pass, $bienvenido, $localidad, $civil, $edad, $provincia, $sexo))
		{
			$t->set_file("pl", "usuario.htm");
			$t->set_var("fondo","_form_user");
			$t->set_var("user", $_SESSION['usuario'] );
			$t->set_var("usuario", $bienvenido);
			$t->set_var("dni", $usuario);
			$t->set_var("localidad", $localidad);
			$t->set_var("civil", $civil);
			$t->set_var("edad", $edad);
			parsear_postulaciones($usuario);
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


function logearce()
{
global $db,$t;
	
//	$t->set_file("pl", "usuario.htm");
	
	
	if(isset($_SESSION['usuario']))
		$usuario = $_SESSION['usuario'];
	else	
		$usuario = $_POST['usuario'];
		
	$pass = $_POST['pass'];
	
	if(verificar_usuario($usuario , $pass, $bienvenido, $localidad, $civil, $edad, $provincia, $sexo))
		{
			$t->set_file("pl", "usuario.htm");
			$t->set_var("fondo","_form_user");
			$t->set_var("usuario", $bienvenido);
			$t->set_var("default", "_default");
			$t->set_var("foto_usuario", $usuario );
			$t->set_var("dni", $usuario);
			
			if($provincia != "")
			{
				$sql_prov = "SELECT * FROM provincias WHERE numero_provincia = ".$provincia;
				$prov = mysql_query($sql_prov);
				$nom_prov = mysql_fetch_array($prov);
				
				$t->set_var("provincia", $nom_prov['nombre_provincia']);
			}
			else
			{
				$t->set_var("provincia", "");
			}	
			
			if($localidad != "")
			{
				$sql_ciu = "SELECT * FROM ciudades WHERE numero_ciudad = ".$localidad;
				$ciu = mysql_query($sql_ciu);
				$nom_ciu = mysql_fetch_array($ciu);
			
				$t->set_var("localidad", utf8_encode($nom_ciu['nombre_ciudad']));
			}
			else
			{
				$t->set_var("localidad","");
			}	
			
			
			$t->set_var("civil", $civil);
			$t->set_var("edad", $edad);
			
			parsear_postulaciones($usuario);
		}
	else
		{
			$t->set_file("pl", "curriculum.htm");
			$t->set_var("mostrar_error_log", "_mostrar");
			parsear_logos_empresas();	
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


function verificar_usuario($usuario , $pass, &$bienvenido, &$localidad, &$civil, &$edad, &$provincia, &$sexo)
{
global $db;

	$passw=md5($pass);
	
	if(isset($_SESSION['usuario']) && $_SESSION['usuario'] == $usuario)
		$sql = "select * from usuario where dni = '".$usuario."'";
	else	
		$sql = "select * from usuario where dni = '".$usuario."' and pass = '".$passw."'";
	
	$result = mysql_query($sql); 
	
	$numero = mysql_num_rows($result);

	$tupla = mysql_fetch_array($result);
	
	$bienvenido = $tupla['nombre']." ".$tupla['apellido'];
	
	$localidad = $tupla['ciudad'];
	
	$sexo = $tupla['sexo'];
	
	$provincia = $tupla['provincia'];
	
	$civil =  $tupla['estadoCivil'];
	
	$edad = edad($tupla['fechaNacimiento']);
	
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
	
	$t->set_var("mostrar", "");
	//$t->set_file("pl", "index.htm");
	header("location:/");
	cargar_areas("");
	cargar_provincias("");
	cargar_ofertas("","");
	
}


function cambiar_imagen()
{
	global $path_site, $t, $db;
	

	$imagen=$_FILES [ 'file' ][ 'tmp_name' ];
	# ruta de la imagen final, si se pone el mismo nombre que la imagen, esta se sobreescribe
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
   $image->resize(51,55);
   $image->save($imagen_final.'/avatar.jpg');

		
	$usuario = $_POST['usuario'];
		
	$t->set_file("pl", "usuario.htm");
	$t->set_var("fondo","_form_user");
	$t->set_var("user", $_SESSION['usuario'] );
	$t->set_var("foto_usuario", $_SESSION['usuario'] );
	
	$t->set_var("usuario", $_POST['usuario']);
	$t->set_var("localidad", utf8_encode($_POST['localidad']));
	$t->set_var("provincia", $_POST['provincia']);
	$t->set_var("civil", $_POST['civil']);
	$t->set_var("edad", $_POST['edad']);
	parsear_postulaciones($_SESSION['usuario']);
	header("location:index.php?action=logearce");
	
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


// Crear fichero nuevo, según extensión.
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
$nombre = $_POST['Cnombre'];
$mail = $_POST['Cmail'];
$consulta = $_POST['Cconsulta'];

 	$cuerpo = "Formulario enviado\n"."<br>";
    $cuerpo .= "Nombre: " . $nombre . "\n"."<br>";
    $cuerpo .= "Email: " . $mail . "\n"."<br>";
    $cuerpo .= "Consulta: " . $consulta . "\n"."<br>";


	$subject = "Consulta, Jobtime";

    //mando el correo...

	$from = "info@jobtime.com.ar";
	$headers = "From: $from";

	
    //mail("rodrigo@jobtime.com.ar",$subject." Formulario recibido",$cuerpo,$headers);
	//mail("info@jobtime.com.ar",$subject." Formulario recibido",$cuerpo);
	enviar_mail(ucwords("jobtime"),"info@jobtime.com.ar","info@jobtime.com.ar","Jobtime - Consulta Web",$cuerpo);

    //doy las gracias por el envío
    echo "Gracias por rellenar el formulario. Se ha enviado correctamente."; 
	
	echo '<SCRIPT language="JavaScript"> window.location="http://www.jobtime.com.ar/index.php?action=mostrar_home_page"  </SCRIPT>';
}


function getUsuario($dni, &$usuario)
{
global $db;

$sql = "select * from usuario where dni = ".$dni;

$consulta = mysql_query($sql);

$ret = mysql_fetch_array($consulta);

$usuario = $ret['nombre'];

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
if(isset($_SESSION['usuario']))
	{
	$t->set_var("empleo_potulado","postularme-");
	//$t->set_var("empleo_potulado","/?action=postular");
	$t->set_var("face","rel='facebox'");
	}
else
	$t->set_var("empleo_potulado","empleo-");	
	//$t->set_var("empleo_potulado","/?action=registrarce");	
	

}

function postulado($user, $id_empleo)
{
global $t, $db;

$sql = "SELECT COUNT(*) AS cant FROM postulaciones WHERE usuario = ".$user." and id_empleo = ".$id_empleo;

$prov = mysql_query($sql);

$postulado = mysql_fetch_array($prov);

if ($postulado['cant'] > 0)
	return true; // si ya esta postulado
else
	return false;

}



function postular()
{
global $t, $db;


	$t->set_file("pl", "postulacion.htm");
	
	$puesto = $_GET['puesto'];
	
	$ciudad = $_GET['ciudad'];
	
	$usuario = $_SESSION['usuario'];
	
	$no_dejar_postular = 'disabled="disabled"';
	
	$sql = "SELECT * FROM empleos, ciudades, empresas, provincias WHERE empleos.ciudad = ciudades.numero_ciudad AND empresas.id_empresa = empleos.id_empresa and empleos.id_empleo = ".$puesto." and ciudad = ".ciudad." GROUP BY empleos.id_empleo";
mysql_query("SET NAMES 'utf8'"); 
$prov = mysql_query($sql);

$ofertas = mysql_fetch_array($prov);

$t->set_var("ciudad",  $ofertas['nombre_ciudad']);
$dealle_ofer =  $ofertas['detalle'];


$t->set_var("detalle", utf8_decode($dealle_ofer));
$t->set_var("titulo", utf8_decode($ofertas['titulo']));
$prov =  getProvincia($ofertas['provincia'], &$nombre_prov);
$t->set_var("provincia", $nombre_prov);
if($ofertas['mlogo'] == 1)
	$t->set_var("logo", $ofertas['logo']);
else
	$t->set_var("logo", "importanteempresa");	
$t->set_var("razon_social", $ofertas['razon_social']);
$t->set_var("id_empleo", $ofertas['id_empleo']);


//busco el nombre del tipo de disponibilidad
	$sql_disponibilidad = "SELECT * FROM disponibilidades WHERE num_disponibilidad = ".$ofertas['disponibilidad'];
	$nom_disponibilidad = mysql_query($sql_disponibilidad);
	$nombre_disponibilidad = mysql_fetch_array($nom_disponibilidad);
	
	$t->set_var("disponibilidad", $nombre_disponibilidad["nombre_disponibilidad"]);


if(postulado($usuario, $ofertas['id_empleo'])) // si el usuario ya esta postulado no de deja volver a postularte
		{
			$t->set_var("desactivar",  $no_dejar_postular);
			$t->set_var("mensaje",  "Usted ya esta postulado en este empleo!");
		}

$t->set_var("usuario", $usuario);


}

function enviar_postulacion()
{
global $t, $db;
//var_dump($_POST);

$usuario = $_POST['usuario'];
$empleo = $_POST['id_empleo'];
$fecha_actual  = date("Y-m-d"); 

$sql = "INSERT INTO postulaciones(id_empleo, usuario, fecha, activo)  VALUES ($empleo, $usuario, '".$fecha_actual."',1)";

mysql_query($sql);

//$t->set_file("pl", "usuario.htm");

header("location:index.php?action=logearce");

}

function enviar_amigo()
{

global $db,$t,$template_cv;

$temp = "/mounted-storage/home129/sub032/sc75253-XNXJ/www.jobtime.com.ar/php_templates/cv";
$t = new Template($temp , "remove");
//$t = new Template($template_cv , "remove");

$t->set_file("pl", "mail_recomendar.html");

$usuario = $_POST['usuario_rec'];

$sql = "SELECT * FROM usuario WHERE dni = ".$usuario;

$result = mysql_query($sql);

$row = mysql_fetch_array($result);

$amigo = utf8_encode($row['nombre']);

$t->set_var("nombre", $amigo);
$t->set_var("mail", $_POST['emailAmigo']);

$sql_empleo = "select * from empleos where id_empleo = ".$_POST['id_empleo_rec'];

$result_empleo = mysql_query($sql_empleo);

$datos_empleo = mysql_fetch_array($result_empleo);

getCiudad($datos_empleo['ciudad'], $ciudad);

$enlace = "buscar-empleo/".$datos_empleo['titulo']; //.$datos_empleo['titulo']."-en-".$ciudad."/".$_POST['id_empleo_rec'];

str_replace(" ","&nbsp;",$enlace); 

$t->set_var("empleo", $datos_empleo['titulo']);

$t->set_var("link", $enlace);

$para = $_POST['emailAmigo'];
//$para = "rodrigo@jobtime.com.ar";
//mando el correo...

$from = "info@jobtime.com.ar";
$body = $t->parse("MAIN", "pl");

enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - $amigo te recomienda un empleo",$body);
enviar_mail(ucwords("jobtime"),$from,"rodrigo@jobtime.com.ar","Jobtime - $amigo te recomienda un empleo",$body);


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
		$palabra = $_GET['empresa'];
	else
		$palabra = $_POST['edit-name'];
	$provincia = $_POST['provincia'];
	$ciudad = $_POST['ciudad'];
	$area = $_POST['areas'];
	$jerarquia = $_POST['jerarquia'];
	$semana = $_POST['fecha'];
}
// para la barra lateral de las busquedas
if(isset($_GET['lateral']))
$area = $_GET['lateral'];

cargar_provincias($provincia);
cargar_ciudades($ciudad);
cargar_areas($area);
cargar_jerarquias($jerarquia);
cargar_categorias($semana);


$sql = "SELECT * FROM empleos, ciudades, empresas WHERE empleos.ciudad = ciudades.numero_ciudad AND empresas.id_empresa = empleos.id_empresa AND empleos.activo = 1";

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
		$sql = $sql." AND puesto_trabajo = ".$area;
	}
if($jerarquia != '')
	{
		$sql = $sql." AND jerarquia = ".$jerarquia;
	}

cargar_ofertas("",$sql);

if(isset($_SESSION['usuario']))
	{
	$t->set_var("empleo_potulado","postularme-");
	//$t->set_var("empleo_potulado","/?action=postular");
	$t->set_var("face","rel='facebox'");
	}
else
	$t->set_var("empleo_potulado","empleo-");
	//$t->set_var("empleo_potulado","/?action=registrarce");	

}

//recuperar pass

function recuperar_pass()
{
global $t;


$t->set_file("pl", "recuperar_contra.htm");


}

function modif_contra()
{

global $t,$db;

$t->set_file("pl", "usuario.htm");

$sql = "SELECT * FROM usuario WHERE dni = ".$_SESSION['usuario'];
		
$result = mysql_query($sql);

$datos_usu = mysql_fetch_array($result);

$dni = $_SESSION['usuario'];

$email = $datos_usu["email"];

$pass = md5($_POST["contra"]);

$passSin = $_POST["contra"];
		
		
$sql = "SELECT * FROM usuario WHERE email = '".$email."' and dni = ".$dni;

$resultMail = mysql_query($sql);



$para  = $email ;


$sqlUp = "UPDATE usuario SET pass = '".$pass."' WHERE email = '".$email."' and dni = ".$dni;

mysql_query($sqlUp);

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


    //mando el correo...

	$from = "info@jobtime.com.ar";
	
	enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Nueva Contraseña",$mensaje);
	
	logearce();

}


function enviar_pass()
{
global $t,$db;



	$email = $_POST["email"];
	$dni = $_POST["dni"];
	#muestra la cantidad de filas
	
	$sql = "SELECT * FROM usuario WHERE email = '".$email."' and dni = ".$dni;

	$resultMail = mysql_query($sql);
	
	$existe=mysql_num_rows($resultMail); 
	
	if($existe > 0)
	{
	
	$str = "ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz1234567890";
	$pass = "";
	for($i=0;$i<12;$i++) {
		$pass .= substr($str,rand(0,62),1);
	}	



$para  = $email ;


$sqlUp = "UPDATE usuario SET pass = '".md5($pass)."' WHERE email = '".$email."' and dni = ".$dni;

mysql_query($sqlUp);

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
	
	enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Nueva Contraseña",$mensaje);
	
	header("location: http://www.jobtime.com.ar");
}
else
	header("location: http://www.jobtime.com.ar");

}



?>
