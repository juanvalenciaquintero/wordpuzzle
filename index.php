

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<style>
    #divJugador
    {
        width:100%;
    }
    #divJugador>div
    {
        width:200px;
        height:100px;
        margin: 100px auto;
        display:flex;
        flex-direction: column;
        justify-content: space-around;
        align-items: center;
    }
    #titulo
    {
        display:flex;
        flex-direction: column;
        justify-content: space-around;
        align-items: center;
    }
    #titulo span, #crono span
    {
        text-align: center;
        font-size:14px;
    }
    #btnJugar
    {
        width:80px;
        font-weight: 400;
        font-size: 16px;
    }
    #jugador
    {
        width:150px;
    }
    #btnJugador
    {
        width:80px;
    }
    #contenedor
    {
        width:170px;
        margin:100px auto;
        display:none;
    }
    #contenedor #palabras input
    {
        width:30px;
        height:30px;
        margin:0;
        padding:0;
        text-align: center;
        font-weight: bold;
        font-size: 24px;
    }
    #crono
    {
        text-align: center;
        margin-top:10px;
    }
</style>
<body>
<div id="divJugador">
    <div>
        <span>¿Quien eres?</span>
        <input type="text" id="jugador">
        <input type="button" id="btnJugador" value="Empezar" onclick="empezar();">
    </div>
</div>
<div id="contenedor">
    <div id="chivato"></div>
    <div id="titulo"></div>
    <div id="crono"></div>
    <div id="palabras">
    <?php
    //Conexion valquinter.com
    // $conexion = mysqli_connect('localhost','valquint_juan','42180200Az','valquint_juegos');
    //Conexión local
    $conexion = mysqli_connect('localhost','root','','diccionario');

    $record = 0;
    $sql = mysqli_query($conexion,"SELECT * FROM wordpuzzle_records WHERE longitud=5 ");
    if (mysqli_num_rows($sql)>0)
    {
        $result = mysqli_fetch_array($sql);
        $record = $result['tiempo'];
    }
    echo "<input id='record' type='hidden' value='" . $record . "'>";

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
</div>
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
        var record = document.getElementById('record').value;
        var html = '<span>Hola <span id="nombre">' + nombre + '</span></span><span>Pulsa el botón para empezar</span><input type="button" id="btnJugar" value="Jugar" onclick="jugar();">';

        if (record>0)
        {
            var min = parseInt(record/60);
            var seg = record%60;
            html += '<span>El record actual es ' + min + ':' + seg + '</span>';
        }
        html += '</span>';
        document.getElementById('titulo').innerHTML = html;
    }

    function jugar()
    {
        timer();
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
            document.getElementById('mensajes').innerHTML = '¡Enhorabuena! Lo has conseguido';
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
            document.getElementById('mensajes').innerHTML = 'Lo siento. La palabra era ' + objPalabra + '. Intentalo de nuevo';
            console.log('Tiempo: ' + tiempo);
            clearTimeout(t);
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