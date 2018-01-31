<?php
require 'vendor/autoload.php';  // PASO 1 CARGAMOS LOS ARCHIVOS NECESARIOS


$app = new Slim\App();

// PASO 2 CONECTAMOS CONTRA NUESTRA BASE DE DATOS

function getConnection() {  
    $dbhost="127.0.0.1";
    $dbuser="root";
    $dbpass="";
    $dbname="test_futbol";
    $dbh = new PDO("mysql:host=$dbhost;dbname=$dbname", $dbuser, $dbpass);
    $dbh->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $dbh;
}


// PASO 3 CREAMOS UNA FUNCION QUE DETECTA SI UN JUGADOR ES ESTRELLA O NO 

function isStar($history){
    $a=0;   // PASO 3.1 INICIALIZAMOS LOS CONTADORES
    $b=0;
    $c=0;
    $d=0;
           $historial = $history["history"]; 



  foreach ($historial as $key => $value) { // PASO 3.2 CREAMOS UN CICLO PARA RECORRER LAS FILAS DE NUESTRA MATRIZ
    $datos = str_split($value);
    $e=0;
  
    foreach ($datos as $posicion => $valor) { // PASO 3.3 CREAMOS UN CICLO PARA RECORRER LAS COLUMNAS 


     //  PASO 3.4 SE VERIFICAN LAS FILAS 
    
    if (isset($datos[$e+1]) && isset($datos[$e+2]) && isset($datos[$e+3]) && $valor == $datos[$e+1] && $valor == $datos[$e+2] && $valor == $datos[$e+3] && ($valor == "G" || $valor == "E")) {
        $b++;
    }
     // PASO 3.5 SE VERIFICA LAS COLUMNAS - DIAGONAL
        
    if (isset($historial[$a+1]) &&  
        isset($historial[$a+2]) && 
        isset($historial[$a+3])){
    $datos2= str_split($historial[$a+1]);
    $datos3= str_split($historial[$a+2]);
    $datos4= str_split($historial[$a+3]);

    // PASO 3.6 SE VERIFICAN LAS COLUMNAS
       
    if (isset($datos2[$e]) && isset($datos3[$e]) && isset($datos4[$e]) && $valor == $datos2[$e] && $valor == $datos3[$e] && $valor == $datos4[$e] && ($valor == "G" || $valor == "E")) {
        $c++;
    }

    // PASO 3.7 Y POR ULTIMO VERIFICAMOS EN FORMA DIAGONAL
        
    if 
        (isset($datos2[$e+1]) &&
         isset($datos3[$e+2]) &&
          isset($datos4[$e+3]) && $valor == $datos2[$e+1] && $valor == $datos3[$e+2] && $valor == $datos4[$e+3] && ($valor == "G" || $valor == "E")) {
        $d++;
    }
    }
    $e++;
    }
    $a++;
}
    if ($b > 0 || $c > 0 || $d > 0) {
        return true;  // ES ESTRELLA 
    }else{
        return false;  // NO ES ESTRELLA
    }
}

// PASO 4 MOSTRAMOS LOS MENSAJES 200 O 403 SEGUN SEA EL CASO Y GUARDAMOS EN LA BASE DE DATOS EL HISTORICO DE PARTIDOS 

$app->post('/star', function ($request, $res){ 

     $response = array(); 
    
    $valor_enviado = $request->getParsedBody(); // PASO 4.1 RECIBO EL VALOR ENVIADO

     
      // PASO 4.2 TRANSFORMO EL VALOR OBTENIDO 


      $partidos = json_decode($valor_enviado['history'], TRUE);

       $variable_guardar = $valor_enviado['history'];

      // PASO 4.3 LLAMO A LA FUNCION ANTES CREADA PARA ENVIAR EL RESPECTIVO MENSAJE

      $estrella = isStar($partidos);

     //  PASO 4.4 HACEMOS LOS RESPECTIVOS CONDICIONALES EN BASE A LA RESPUESTA OBTENIDA POR LA FUNCION 

      if($estrella == false){
    $response["status"] = 403;
    $response["error"] = true;
    $response["message"] = 'NO ES ESTRELLA ';
    $status=403;
    } else {
    $response["status"] = 200;
    $response["error"] = false;
    $response["message"] = "ES UNA ESTRELLA";
    $status=200;
    }

    // 4.5 GUARDO LA INFORMACION EN LA TABLA 
    
    $sql = "INSERT INTO partidos (partidos, estrella) VALUES ('$variable_guardar', '$estrella')";
    try {
        $db = getConnection();
        $stmt = $db->prepare($sql);
        $stmt->bindParam("partidos", $emp->partidos);
        $stmt->bindParam("estrella", $emp->estrella);
        
        $stmt->execute();
        $emp->id = $db->lastInsertId();
        $db = null;
        return $res->withStatus($status)->withJson($response);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});



// PASO 5 CREAMOS UNA FUNCION PARA VER LA CANTIDAD DE JUGADORES ESTRELLAS 
$app->get('/stats', function ($response){  
    $sql = "SELECT (SELECT COUNT(p.estrella) FROM partidos AS p WHERE p.estrella = 1) AS cantidad_estrellas, (SELECT COUNT(pdos.estrella) FROM partidos AS pdos WHERE pdos.estrella = 0) AS cantidad_no_estrellas FROM partidos LIMIT 1";
    try {
        $stmt = getConnection()->query($sql);
        $estadisticas = $stmt->fetchAll(PDO::FETCH_OBJ);
        $db = null;
        
        return json_encode($estadisticas);
    } catch(PDOException $e) {
        echo '{"error":{"text":'. $e->getMessage() .'}}';
    }
});


$app->run();

?>