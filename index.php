<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="estilos.css">
    <title>Document</title>
</head>
<body>
<?php
 //Conexion valquinter.com
// $conexion = mysqli_connect('localhost','valquint_juan','42180200Az','valquint_juegos');

//Conexión local
$conexion = mysqli_connect('localhost','root','','diccionario');

$loginVisible = 'style="display:none"';
if (!isset($_POST['nombre']))
{
    $loginVisible = 'style="display:block"';
}
?>
<div id="divJugador" <?php echo $loginVisible; ?>>
    <div>
        <form id="formLogin" method="post">
            <span>¿Quien eres?</span>
            <input type="text" id="jugador" name="nombre" value="<?php echo (isset($_POST['nombre']) ? $_POST['nombre'] : '');  ?>">
            <input type="hidden" id="long" name="dificultad" value="<?php echo (isset($_POST['dificultad']) ? $_POST['dificultad'] : '');  ?>">
            <input type="button" id="btnJugador" value="Empezar" onclick="empezar();">
        </form>
    </div>
</div>
<?php

?>
<div id="contenedor">
    <div id="chivato"></div>
    <div id="titulo">
    <?php
    if (isset($_POST['nombre']) && (isset($_POST['dificultad'])))
    {
        $record = 0;
        $longitud = $_POST['dificultad'];
        $sql = mysqli_query($conexion,"SELECT * FROM wordpuzzle_records WHERE longitud=" . $longitud);
        if (mysqli_num_rows($sql)>0)
        {
            $result = mysqli_fetch_array($sql);
            $record = $result['tiempo'];
        }
    ?>
        <span id="jugador">Hola <?php  echo $_POST['nombre']; ?></span>
        <br>
        <span>El record actual para este nivel es de <?php echo $record; ?> segundos</span>
        <input type="button" id="btnJugar" value="Jugar" onclick="jugar();">


    </div>
    <div id="crono"></div>
    <div id="palabras">
    <?php
    echo "<input id='record' type='hidden' value='" . $record . "'>";

    $sql = mysqli_query($conexion,"SELECT * FROM wordpuzzle_palabras WHERE longitud=$longitud ORDER BY RAND() LIMIT 1");
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
</div>
<?php
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
        var palabra = document.getElementById(selector);
        console.log(selector);
        if (palabra)
        {
            document.getElementById(selector).focus();
        }


    })();

      var sec=0;
      var min=0;
      var t;
      function tick()
      {
        sec++;
        if (sec >= 60)
        {
          sec = 0;
          min++;
        }
      }

      function timer() {
        t = setTimeout(add, 1000);
      }

      function add() {
        tick();
        document.getElementById('crono').innerHTML = '<span id="tiempo">' + (min > 9 ? min : "0" + min)
        + ":" + (sec > 9 ? sec : "0" + sec) + '</span>';
        timer();
      }
    function empezar()
    {
        document.getElementById('divJugador').style.display="none";
        document.getElementById('contenedor').style.display="block";
        var nombre = document.getElementById('jugador').value;
        // var record = document.getElementById('record').value;
        // var html = '<span>Hola <span id="nombre">' + nombre + '</span></span><span>Pulsa el botón para empezar</span><input type="button" id="btnJugar" value="Jugar" onclick="jugar();">';
        var html = '<span>Hola <span id="nombre">' + nombre + '</span><br>';
        html += '<form id="formDificultad" method="post"><select id="dificultad" name="dificultad" onchange="selectDif();"><option value="0">Selecciona la dificultad</option><option value="4">4 letras</option><option value="5">5 letras</option><option value="6">6 letras</option><option value="7">7 letras</option><option value="8">8 letras</option><option value="9">9 letras</option></select><input type="hidden" name="nombre" value="' + nombre + '"></form></span>';

        // if (record>0)
        // {
        //     var min = parseInt(record/60);
        //     var seg = record%60;
        //     html += '<span>El record actual es ' + min + ':' + seg + '</span>';
        // }
        // html += '</span>';
        document.getElementById('titulo').innerHTML = html;
    }

    function jugar()
    {

        timer();
    }

    function selectDif()
    {
        document.getElementById('formDificultad').submit();
    }

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
        var objetivo   = new Array();
        var palabra    = new Array();
        var aciertos   = 0;
        var objPalabra = "";
        var tiempo     = 0;
        var longitud   = 5;
        for (i=0;i<5;i++)
        {
          objetivo[i]=document.getElementById('objetivo_'+i).value;
        }
        // document.getElementById('chivato').innerHTML=fila;
        for (i=0;i<5;i++)
        {

          objPalabra += objetivo[i];
          palabra[i]=document.getElementById('palabra_'+fila+'_'+i).value;
          console.log('Objetivo: ' + objetivo[i] + ' - ' + 'Palabra: ' + palabra[i]);
          console.log('Selector: ' + 'palabra_'+fila+'_'+i);
          if (objetivo[i] == palabra[i])
          {
             console.log('Entrado en if: ');
             document.getElementById('palabra_'+fila+'_'+i).style.backgroundColor='#ACFF33';
             aciertos++;
          } else if (inArray(palabra[i],objetivo))
          {
            document.getElementById('palabra_'+fila+'_'+i).style.backgroundColor='#FFFC33';
          }
        }
        tiempoHtml = document.getElementById('tiempo').innerHTML;
        tiempoHtml = tiempoHtml.split(':');
        tiempo = parseInt(tiempoHtml[0]*60) + parseInt(tiempoHtml[1]);
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
            var jugador = document.getElementById('jugador').innerHTML;
            document.getElementById('mensajes').innerHTML = '¡Enhorabuena! Lo has conseguido<br><input type="button" value="Volver a jugar" onclick="jugarOtra();">';
            console.log('Tiempo: ' + tiempo);
            clearTimeout(t);
            var jugador = document.getElementById('nombre').innerHTML;
            var xHttp = new XMLHttpRequest();
            console.log(jugador + ' - ' + longitud + ' - ' + tiempo);
            xHttp.onreadystatechange = function() {
              if ((xHttp.readyState === 4) && (xHttp.status === 200))
              {
                  console.log(xHttp.responseText);
              }
            }
            xHttp.open('POST', 'peticiones.php', true);
            xHttp.setRequestHeader("Content-type", "application/json");
            xHttp.send(JSON.stringify({
              "jugador"  : jugador,
              "longitud" : longitud,
              "tiempo"   : tiempo
            }));
        } else if(fila==6)
        {
            document.getElementById('mensajes').innerHTML = 'Lo siento. La palabra era ' + objPalabra + '. Intentalo de nuevo<br><input type="button" value="Volver a jugar" onclick="jugarOtra();">';
            console.log('Tiempo: ' + tiempo);
            clearTimeout(t);
        }
    }

    function jugarOtra()
    {

        document.getElementById('formLogin').submit();
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