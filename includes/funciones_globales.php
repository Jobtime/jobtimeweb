<?php

function getEstudio($id)
{
global $db,$t;

$sql = "select * from tipo_estudio where numero_estudio = ".$id;

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$nombre = $resultado["nombre_estudio"];

return $nombre;

}


function getEstado($id)
{
global $db,$t;

$sql = "select * from estados where numero_estado = ".$id;

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$nombre = $resultado["nombre_estado"];

return $nombre;


}

function getInstitucion($id)
{
global $db,$t;

$sql = "select * from universidades where numero_universidad = ".$id;

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$nombre = $resultado["nombre_universidad"];

return $nombre;

}

function getRamo($id)
{
global $db,$t;

$sql = "select * from ramos where numero_ramo = ".$id;

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$nombre = $resultado["nombre_ramo"];

return $nombre;

}

function getActividad($id)
{
global $db,$t;

$sql = "select * from jerarquias where id_jerarquia = ".$id;

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$nombre = $resultado["nombre_jerarquia"];

return $nombre;


}

function getPais($id)
{
global $db,$t;

$sql = "select * from paises where numero_pais = ".$id;

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$nombre = $resultado["nombre_pais"];

return $nombre;

}

function getCiudad($id)
{
global $db,$t;

$sql = "select * from ciudades where numero_ciudad = ".$id;

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$nombre = $resultado["nombre_ciudad"];

return $nombre;


}

function get_ciudad($id,$id_ciudad)
{
    global $db,$t;
    
    $sql = "select * from ciudades where numero_ciudad = ".$id_ciudad;

    $result = mysqli_query($db,$sql);

    $resultado = mysqli_fetch_array($result);

    $nombre = $resultado["nombre_ciudad"];

    return $nombre;

}

function get_provincia($db,$id_provincia)
{
	$sql = "SELECT nombre_provincia FROM provincias WHERE numero_provincia = ".$id_provincia;
	$result = mysqli_query($db,$sql);
	$row = mysqli_fetch_array($result);
	
	return $row["nombre_provincia"];
	
}

function getProvincia($id )
{
global $db;

$sql = "select nombre_provincia from provincias where numero_provincia = ".$id;

$result = mysqli_query($db,$sql);

$resultado = mysqli_fetch_array($result);

$nombre = $resultado["nombre_provincia"];

return $nombre;

}





function generar_pdf()
{
global $db,$t;


$usuario = $_SESSION['usuario'];

require('fpdf.php');
//Create a new PDF file
$pdf=new FPDF();
$pdf->AddPage();

$sql = "select * from usuario where dni = ".$usuario." ORDER BY apellido";

$sql_estudios = "select * from estudios where dni = ".$usuario;

$sql_laboral = "select * from laboral where dni = ".$usuario;


$resul_laboral = mysqli_query($db,$sql_laboral);

$resul_estudio = mysqli_query($db,$sql_estudios);


#muestra la cantidad de filas
$contadorEstudios=mysqli_num_rows($resul_estudio); 

$contadorLaboral=mysqli_num_rows($resul_laboral); 


//Select the Products you want to show in your PDF file
$result=mysqli_query($db,$sql);
$number_of_products = mysqli_numrows($result);

//Initialize the 3 columns and the total
$column_dni = "";
$column_nombre = "";
$column_apellido = "";
$column_email  = "";
$total = 0;


//For each row, add the field to the corresponding column

while($row = mysqli_fetch_array($result))
{
    $dni = $row["dni"];
    $nombre = $row["nombre"];
    $apellido = $row["apellido"];
	$mail = $row["email"];
	$nacimiento = $row["fechaNacimiento"];
	$civil = $row["estadoCivil"];
	if($civil == 1)
		$estado_civil = "Soltero/a";
	elseif($civil == 2)	
		$estado_civil = "Casado/a";
	elseif($civil == 3)	
		$estado_civil = "Concubinato/a";
	elseif($civil == 4)	
		$estado_civil = "Viudo/a";		
	$hijos = $row["hijos"];
	$pais= $row["pais"];
	$ciudad = $row["ciudad"];
	$provincia = $row["provincia"];
	$domicilio = $row["domicilio"];
	$telefono = $row["telefonoContacto"];
    //$price_to_show = number_format($row["Price"],',','.','.');

    $column_dni = $column_dni.$dni."\n";
    $column_nombre = $column_nombre.$nombre."\n";
    $column_apellido = $column_apellido.$apellido."\n";
	$column_email = $column_email.$mail."\n";

    //Sum all the Prices (TOTAL)
   // $total = $total+$real_price;
}
//mysql_close();

//Convert the Total Price to a number with (.) for thousands, and (,) for decimals.
$total = number_format($total,',','.','.');



//Fields Name position
$Y_Fields_Name_position = 20;
//Table position, under Fields Name
$Y_Table_Position = 40;

//First create each Field Name
//Gray color filling each Field Name box
$pdf->SetFillColor(255,255,255);
$pdf->SetDrawColor(255,255,255);
//Bold Font for Field Name
$pdf->SetY($Y_Table_Position);


getProvincia($provincia, $nombre_provincia);
getPais($pais, $nombre_pais);
getCiudad($ciudad, $nombre_ciudad);


$pdf->SetFont('Arial','B',12);
$pdf->SetX(10);
$pdf->SetTextColor(16,166,33);
$pdf->Ln();
$pdf->Cell(50,6,'Datos Personales:',1,0,'L',1);
$pdf->Ln();
$pdf->SetTextColor(0,0,0);
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'DNI: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(20);
$pdf->Cell(80,6,$column_dni,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Nombre: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(30);
$pdf->Cell(80,6,$column_nombre,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Apellido: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(30);
$pdf->Cell(80,6,utf8_decode($column_apellido),1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Email: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(25);
$pdf->Cell(80,6,$column_email,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Fecha de Nacimiento: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(55);
$pdf->Cell(80,6,$nacimiento,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Estado Civil: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(37);
$pdf->Cell(80,6,$estado_civil,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Hijos: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(23);
$pdf->Cell(80,6,$hijos,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Pais: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(22);
$pdf->Cell(80,6,$nombre_pais,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Ciudad: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(27);
$pdf->Cell(80,6,$nombre_ciudad,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Provincia: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(32);
$pdf->Cell(80,6,$nombre_provincia,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Domicilio: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(32);
$pdf->Cell(80,6,$domicilio,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Tel�fono: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(32);
$pdf->Cell(80,6,$telefono,1,0,'L',1);
$pdf->Ln();

$pdf->Image('/mounted-storage/home129/sub032/sc75253-XNXJ/www.jobtime.com.ar/images/cabecera_pdf.jpg',30,8,150);
$pdf->Image('/mounted-storage/home129/sub032/sc75253-XNXJ/www.jobtime.com.ar/usuarios/'.$dni.'/avatar.jpg',155,40,10);



$idiomasJuntos="";
$idiomatmp="";
$congresosJuntos ="";

if($contadorEstudios > 0)
{
$pdf->Ln();
$pdf->Ln();
$pdf->SetTextColor(16,166,33);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,6,'Estudios:',1,0,'L',1);
$pdf->Ln();
$pdf->SetTextColor(0,0,0);

while($row_estudio = mysqli_fetch_array($resul_estudio))
{

$estudio = $row_estudio["estudio"];
$titulo = $row_estudio["titulo"];
$estado = $row_estudio["estado"];
$ingreso = $row_estudio["anioIngreso"];
$egreso = $row_estudio["anioEgreso"];
$institucion = $row_estudio["institucion"];
$idioma = $row_estudio["idioma"];
$idiomas = $row_estudio["idiomas"];
$congresos = $row_estudio["congresos"];
$nivel_idioma = $row_estudio["nivel"];

getEstudio($estudio, $nombre_estudio);

getEstado($estado, $nombre_estado);
getInstitucion($institucion, $nombre_institucion);
if($nombre_institucion == "Otra")
	$otra_inst =  $row_estudio["otra_istitucion"];
else
	$otra_inst	= $nombre_institucion;


$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Estudio: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(29);
$pdf->Cell(80,6,$nombre_estudio,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Titulo: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(25);
$pdf->Cell(80,6,utf8_decode($titulo),1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Estado: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(27);
$pdf->Cell(80,6,$nombre_estado,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'A�o Ingreso: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(37);
$pdf->Cell(80,6,$ingreso,1,0,'L',1);
$pdf->SetFont('Arial','B',12);
$pdf->SetX(47);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'A�o Egreso: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(75);
$pdf->Cell(80,6,$egreso,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Institucion: ',1,0,'P',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(35);
$pdf->Cell(80,6,$otra_inst,1,0,'P',1);
$pdf->Ln();
$pdf->Ln();
if($idiomas == "Ninguno")
	{
		if($idioma != "Ninguno" || $idioma != "")
			if($idiomatmp != $idioma)
			{
				$idiomasJuntos = $idiomasJuntos."  Nivel: ".$nivel_idioma." | Otros: ".$idioma;
				$idiomatmp = $idioma;
			}
	}
else
{
	if($idioma != "Ninguno" || $idioma != "")
			if($idiomatmp != $idioma)
			{
				$idiomasJuntos = $idiomasJuntos.$idioma."  Nivel: ".$nivel_idioma." | Otros: ".$idiomas;
				$idiomatmp = $idioma;
			}
}					
if($congresos == "Ninguno" || $congresos == "")
	$congresosJuntos = $congresos;
else
	$congresosJuntos = $congresosJuntos.", ".$congresos;	
}

if($congresosJuntos == "")
	$congresosJuntos = "Ninguno";
	
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Idiomas: ',1,0,'P',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(30);
$pdf->Cell(80,6,$idiomasJuntos,1,0,'P',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Congresos: ',1,0,'P',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(36);
$pdf->Cell(80,6,$congresosJuntos,1,0,'P',1);
$pdf->Ln();
$pdf->Ln();


}

if($contadorLaboral > 0)
{
$pdf->Ln();
$pdf->SetFont('Arial','B',12);
$pdf->SetTextColor(16,166,33);
$pdf->Cell(50,6,'Experiencias Laborales:',1,0,'L',1);
$pdf->Ln();
$pdf->SetTextColor(0,0,0);

$conocimientosCompi = "";

while($row_laboral = mysqli_fetch_array($resul_laboral))
{

$empresa = $row_laboral["empresa"];
$desde = $row_laboral["desde"];
$hasta = $row_laboral["hasta"];
$referencias = $row_laboral["referencias"];
$actividad = $row_laboral["actividad"];
$responsabilidades = $row_laboral["responsabilidades"];
$motivoEgreso = $row_laboral["motivoEgreso"];
$ramo = $row_laboral["ramo"];

if($row_laboral["conocimientos"] != "")
	$conocimientosCompi = $conocimientosCompi.$row_laboral["conocimientos"].", ";

if($motivoEgreso == "")
$motivoEgreso = "Ninguno";

getActividad($actividad, $nombre_actividad);
getRamo($ramo, $nombre_ramo);

$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Empresa: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(32);
$pdf->Cell(80,6,utf8_decode($empresa),1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Desde: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(25);
$pdf->Cell(80,6,$desde,1,0,'L',1);
$pdf->SetX(35);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Hasta: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(49);
$pdf->Cell(80,6,$hasta,1,0,'L',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Referencias: ',1,0,'P',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(36);
$pdf->Cell(80,6,utf8_decode($referencias),1,0,'P',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Actividad: ',1,0,'P',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(32);
$pdf->Cell(80,6,$nombre_actividad,1,0,'P',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Responsabilidades: ',1,0,'P',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(51);
$pdf->Cell(80,6,utf8_decode($responsabilidades),1,0,'P',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Motivo Egreso: ',1,0,'P',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(42);
$pdf->Cell(80,6,utf8_decode($motivoEgreso),1,0,'P',1);
$pdf->Ln();
$pdf->SetX(10);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,6,'Rama: ',1,0,'L',1);
$pdf->SetFont('Arial','',10);
$pdf->SetX(25);
$pdf->Cell(80,6,$nombre_ramo,1,0,'L',1);
$pdf->Ln();
$pdf->Ln();
}

if($conocimientosCompi != "")
{

$pdf->Ln();
$pdf->SetTextColor(16,166,33);
$pdf->SetFont('Arial','B',12);
$pdf->Cell(50,6,'Conocimientos:',1,0,'P',1);
$pdf->SetFont('Arial','',10);
$pdf->Ln();
$pdf->SetTextColor(0,0,0);

$tamTex = strlen($conocimientosCompi);
$tam = 110;
$veces = $tamTex / $tam;
$cantidad = round($veces); 
$com = 0;
/*echo "hasta:".$cantidad."<br>";
 exit;*/

for($i = 0; $i <= $cantidad; $i++)
{
$mensajePre = substr($conocimientosCompi, $com, $tam);
$pdf->SetX(10);
$pdf->Cell(10,6,utf8_decode($mensajePre),1,0,'P',1);
$pdf->Ln();
$com= $com+110;
$tam= $tam;
}


$pdf->Ln();
}

}


$pdf->Output();


}

function enviar_mail($from_name,$from,$to,$subject,$mensaje)
{

	require_once("class.phpmailer.php");

	$mail = new phpmailer;

	$mail->IsHTML(1);
	$mail->CharSet		= "iso-8859-1";
	$mail->IPAddress	= getenv("REMOTE_ADDR");
	$mail->timezone		= "-0000";
	$mail->From 		= $from;
	$mail->Sender 		= $from;
	//$mail->Mailer		= "smtp";
	$mail->FromName		= $from_name;
	$mail->Host 		= "www.jobtime.com.ar"; //smtp1.servage.net
	/*$mail->SMTPAuth		= true;
	$mail->Username		= "rodrigo@jobtime.com.ar";
	$mail->Password		= "m4m429204644";*/
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = "smtp.ionos.es"; 
	$mail->Username = "info@jobtime.com.ar"; 
	$mail->Password = "EmmaColella2018"; 
	$mail->Port = 587; 
	$mail->WordWrap 	= 76;
	$mail->Priority		= 3;
	$mail->ContentType  = "text/html";
	$mail->AddCustomHeader("X-MimeOLE: Produced by Jobtime");
	

	$array_mails=explode(";",$to);
	for($i=0;$i<count($array_mails);$i++) 
	{
		$email = $array_mails[$i];
		$mail->AddAddress($email);
		//echo "<hr>mail:$email<br>";
	}

	$mail->Subject = $subject;
	$mail->Body = $mensaje;
	
	
	$exito = $mail->Send();

	if(!$exito){
			mail('rodrigo@jobtime.com.ar', 'error enviando mail', "");
			echo "<br>Problemas enviando correo electronico, póngase en contacto a info@jobtime.com.ar e indiquenos su problema. Disculpe las molestas <br>";
			echo "<br>".$mail->ErrorInfo;
			exit;
	}

}

function enviar_mail_dos($from_name,$from,$to,$subject,$mensaje)
{

	require_once("class.phpmailer.php");

	$mail = new phpmailer;

	$mail->IsHTML(1);
	$mail->CharSet		= "iso-8859-1";
	$mail->IPAddress	= getenv("REMOTE_ADDR");
	$mail->timezone		= "-0000";
	$mail->From 		= $from;
	$mail->Sender 		= $from;
	//$mail->Mailer		= "smtp";
	$mail->FromName		= $from_name;
	$mail->Host 		= "www.jobtime.com.ar"; //smtp1.servage.net
	/*$mail->SMTPAuth		= true;
	$mail->Username		= "rodrigo@jobtime.com.ar";
	$mail->Password		= "m4m429204644";*/
	$mail->IsSMTP();
	$mail->SMTPAuth = true;
	$mail->Host = "smtp.ionos.es"; 
	$mail->Username = "info@jobtime.com.ar"; 
	$mail->Password = "EmmaColella2018"; 
	$mail->Port = 587; 
	$mail->WordWrap 	= 76;
	$mail->Priority		= 3;
	$mail->ContentType  = "text/html";
	$mail->AddCustomHeader("X-MimeOLE: Produced by Jobtime");
	

	$array_mails=explode(";",$to);
	for($i=0;$i<count($array_mails);$i++) 
	{
		$email = $array_mails[$i];
		$mail->AddAddress($email);
		//echo "<hr>mail:$email<br>";
	}

	$mail->Subject = $subject;
	$mail->Body = $mensaje;
	
	
	$exito = $mail->Send();

	if(!$exito){
			mail('info@jobtime.com.ar', 'error enviando mail', "");
			echo "<br>Problemas enviando correo electronico, póngase en contacto a info@jobtime.com.ar e indiquenos su problema. Disculpe las molestas <br>";
			echo "<br>".$mail->ErrorInfo;
			exit;
	}

}

function mail_php($para,$mensaje,$titulo)
{

  
$cabeceras = 'MIME-Version: 1.0' . "\r\n";
$cabeceras .= 'Content-type: text/html; charset=utf-8' . "\r\n";
$cabeceras .= 'From: Jobtime<mail@jobtime.com.ar>';

    //mando el correo...

	$from = "info@jobtime.com.ar";
	
	$enviado = mail($para, $titulo, $mensaje, $cabeceras);
 /*
if ($enviado)
  echo 'Email enviado correctamente';
else
  echo 'Error en el envío del email';  */
}

?>