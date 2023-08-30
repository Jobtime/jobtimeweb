<?php 
require_once("funciones_globales.php");

global $db;

 $mysql_host = "mysql1060.servage.net";
 $mysql_username = "jobtime";
 $mysql_passwd = "m4m429204644";
 $mysql_database = "jobtime";

mysql_pconnect($mysql_host, $mysql_username, $mysql_passwd) or die ("No conseguí conectar!");
mysql_select_db($mysql_database) or die ("No conseguí conectar con la base de datos!");


$sql = "select count(*) as cantidad from usuario";

$result = mysql_query($sql);

$resultado = mysql_fetch_array($result);

$cantidad_usuarios = $resultado['cantidad'];

$fecha_actual  = date("Y-m-d");

$sql_ud = "select count(*) as cantidad_ud from usuario where alta = '".$fecha_actual."'";

$result_ud = mysql_query($sql_ud);

$resultado_ud = mysql_fetch_array($result_ud);

$usuarios_hoy = $resultado_ud['cantidad_ud'];

$from = "info@jobtime.com.ar";

$para = "info@jobtime.com.ar";

$body = '
<html>
<head>
  <title>Estadisticas de JobTime.com.ar del dia "'.$fecha_actual.'"</title>
</head>
<body>
  <p>Cantidad de Usuarios en el dia de ayer "'.$fecha_actual.'" : '.$usuarios_hoy.'</p>
  <table width="560" border="0">
   <tr>
    <td><p>Total de Usuarios: '.$cantidad_usuarios.' <br /></p>
   </td>
  </tr>
</table>
</body>
</html>
';

enviar_mail(ucwords("jobtime"),$from,$para,"Jobtime - Estatisticas del dia",$body);



?>