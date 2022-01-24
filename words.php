<?php

$conexion = mysqli_connect('localhost','root','','diccionario');

// $sql = mysqli_query($conexion,"SELECT * FROM palabras");
// if (mysqli_num_rows($sql)==0)
// {
//     echo 'No hay registros';
// } else
// {
//     $result = mysqli_fetch_array($sql) ;
//     echo $result['palabra'];
// }

$fp = fopen("spanish1.txt", "r");
while (!feof($fp)){
    $linea = fgets($fp);
    $lineaLimpìa = str_replace('*','',$linea);
    $lineaLimpìa = str_replace('#','',$lineaLimpìa);
    $lineaLimpìa = trim($lineaLimpìa);
    // echo $lineaLimpìa . '<br>';
    $palabras[] = $lineaLimpìa;

   mysqli_query($conexion,"INSERT INTO palabras (palabra,longitud) VALUES ('$lineaLimpìa',". strlen($lineaLimpìa).")");


}
// for ($i=100;$i<220;$i++)
// {
//     echo $palabras[$i] . ' - ' . strlen( $palabras[$i] ) . '<br>';
// }
// echo count($palabras);

// var_dump($palabras);
fclose($fp);


?>