

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    #contenedor
    {
        width:180px;
        margin:100px auto;
    }
    input
    {
        width:30px;
        height:30px;
        margin:0;
        padding:0;
        text-align: center;
        font-weight: bold;
        font-size: 24px;
    }
</style>
<body>
<div id="contenedor">
<div id="chivato"></div>
<?php

    $conexion = mysqli_connect('localhost','valquint_juan','42180200Az','valquint_juegos');

    $sql = mysqli_query($conexion,"SELECT * FROM wordpuzzle_palabras WHERE longitud=5 ORDER BY RAND() LIMIT 1");
    $result = mysqli_fetch_array($sql);
    $palabra = str_split($result['palabra']);
    // echo $result['palabra'];
    for ($i=0;$i<5;$i++)
    {
      echo "<input class='letra'  id='objetivo_" .$i . "' type='hidden' value='" . strtoupper($palabra[$i]) . "'>";
    }
    echo '<br>';


    for ($i=0;$i<7;$i++)
    {
        for ($j=0;$j<5;$j++)
        {
            echo "<input class='letra'  id='palabra_" .$i . "_" . $j . "' type='text' onkeyup='checkLetra(event);' >";
        }
        echo '<br>';
    }


?>
<div id="mensajes"></div>
</div>
</body>
</html>

<script>
    (function(){
        var fila = 0;
        var columna = 0;
        var selector = 'palabra_' + fila + '_' + columna;
        console.log(selector);
        document.getElementById(selector).focus();
    })();
    function checkLetra(event)
    {
        var id = event.target.id;
        var letra = document.getElementById(id).value.toUpperCase();
        document.getElementById(id).value = letra;
        var posiciones = id.split('_');
        var fila = posiciones[1];
        var columna = posiciones[2];
        // console.log(fila + ' - ' + columna);
        selector = 'palabra_' + fila + '_' + columna;
        // console.log('selector: ' + selector);
        document.getElementById(selector).disabled = true;
        columna++;
        // console.log(fila + ' - ' + columna);
        // document.getElementById('chivato').innerHTML=fila;
        if (columna == 5)
        {
            checkPalabra(fila);
        } else
        {
            selector = 'palabra_' + fila + '_' + columna;
            // console.log('Nuevo selector: ' + selector);
            document.getElementById(selector).focus();
        }


    }

    function checkPalabra(fila)
    {
        var objetivo = new Array();
        var palabra  = new Array();
        var aciertos = 0;
        var objPalabra ="";
        for (i=0;i<5;i++)
        {
          objetivo[i]=document.getElementById('objetivo_'+i).value;
        }
        document.getElementById('chivato').innerHTML=fila;
        for (i=0;i<5;i++)
        {
          
          objPalabra += objetivo[i];
          palabra[i]=document.getElementById('palabra_'+fila+'_'+i).value;
          console.log('Objetivo: ' + objetivo[i] + ' - ' + 'Palabra: ' + palabra[i]);
          console.log('Selector: ' + 'palabra_'+fila+'_'+i);
          if (objetivo[i] == palabra[i])
          {
             console.log('Entrado en if: ')
             document.getElementById('palabra_'+fila+'_'+i).style.backgroundColor='#ACFF33';
             aciertos++;
          } else if (inArray(palabra[i],objetivo))
          {
            document.getElementById('palabra_'+fila+'_'+i).style.backgroundColor='#FFFC33';
          }
        }
        console.log('Aciertos: ' + aciertos);
        if (aciertos<5 && fila<6)
        {
            ++fila;
            columna=0;
            selector = 'palabra_' + fila + '_' + columna;
            document.getElementById(selector).focus();
            document.getElementById('mensajes').innerHTML=fila;
        } else if(aciertos==5)
        {
            document.getElementById('mensajes').innerHTML = '¡Enhorabuena! Lo has conseguido';
        } else if(fila==6)
        {
            document.getElementById('mensajes').innerHTML = 'Lo siento. La palabra era ' + objPalabra + '. Intentalo de nuevo';
        }
    }

    function inArray(needle, haystack) {
        var length = haystack.length;
        for(var i = 0; i < length; i++) {
            if(typeof haystack[i] == 'object') {
                if(arrayCompare(haystack[i], needle)) return true;
            } else {
                if(haystack[i] == needle) return true;
            }
        }
        return false;
    }
</script>