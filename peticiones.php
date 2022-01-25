<?php
//Conexion valquinter.com
// $conexion = mysqli_connect('localhost','valquint_juan','42180200Az','valquint_juegos');
//Conexión local
$conexion = mysqli_connect('localhost','root','','diccionario');

$request  = json_decode(file_get_contents('php://input'));

$fp = fopen('chivato.txt','w');
fwrite($fp,print_r($request,true). "
");

if (isset($request->jugador))
{
    fwrite($fp,"SELECT * FROM wordpuzzle_records WHERE longitud=" . $request->longitud . "
");
    $sql = mysqli_query($conexion,'SELECT * FROM wordpuzzle_records WHERE longitud=' . $request->longitud);
    if (mysqli_num_rows($sql)>0)
    {
        $result = mysqli_fetch_array($sql);
        if ($result['tiempo']>$request->tiempo)
        {
            mysqli_query($conexion,"UPDATE wordpuzzle_records SET jugador='" . $request->jugador . "',tiempo=" . $request->tiempo . " WHERE longitud=" . $request->longitud );
        }
        fclose($fp);
        echo $request->tiempo;
    } else
    {
        fwrite($fp,"INSERT INTO wordpuzzle_records (jugador,longitud,tiempo) VALUES ('" . $request->jugador . "'," . $request->longitud . "," . $request->tiempo . ")");
        mysqli_query($conexion,"INSERT INTO wordpuzzle_records (jugador,longitud,tiempo) VALUES ('" . $request->jugador . "'," . $request->longitud . "," . $request->tiempo . ")");
        fclose($fp);
        echo 'Nuevo record';
    }
}


?>