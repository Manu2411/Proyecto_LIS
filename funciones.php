<?php

/*  Proyecto AutoCube BibliotecaUDB para la 
    cátedra de Lenguajes Interpretados en el Servidor
    En este archivo se almacenan la mayoría de funciones
    recurrentes del sistema descritas con los comentarios */

       
    //función que obtiene la conexión al servidor y selecciona la base de datos
    function conectar(){
        //usuario creado con privilegios limitados
        $user = "root";
        $pass = "";
        $server = "localhost";
        
        //base de datos
        $basededatos = "lisproject_db";
        
        //sentencia mysql y validación
        $q = mysqli_connect($server, $user, $pass, $basededatos, '3308') or die ("No se pudo establecer coexión al servidor: " . mysqli_error());
        
        // //selección de base de datos
        // $db = mysqli_select_db($q, $basededatos);
        return $q;
    }

    function consultaSolicitudes($elementoID, $cRID){
        //se utiliza la conexión a la bd
        $con = conectar();

        //sentencia para buscar en la tabla <solicitudes> las solicitudes o reservas para el cubículo específico
        $q = "select * from solicitudes where elementoID='$elementoID' and cRID=$cRID ORDER BY solicitudID"; 

        //se almacena el resultado
        $r = mysqli_query($con, $q);

        return $r;
    }

    function obtenerReservas($cRID, $elementoID){
        
        //se obtiene la cantidad de reservas
        $r = consultaSolicitudes($elementoID, $cRID);
        
        //cantidad de resultados
        $existentes = mysqli_num_rows($r);

        //se evalua la cantidad de resultados (reservas para este cubiculo)
        if($existentes>0){
            //mientras existan registros(reservas) obtendra sus datos
            while($columna = mysqli_fetch_array($r))
                {
                    //se almacenan los datos de cada reserva
                    $carnet = $columna['carnet'];
                    $entrada = $columna["entrada"];
                    $salida = $columna['salida'];
                    
                    //se formatea la impresion
                    echo "<li><span>" . $carnet . "&nbsp;&nbsp;</span><span>&nbsp;&nbsp;";
                    echo $entrada . " - " . $salida . "</span></li><br>\n";
                }
        }
        else{
            echo "No hay reservas para este cubículo"; //en caso de no haber registros se envía el mensaje
        }
    }

    function reservasEnCola($elementoID, $fecha){
        $con = conectar();
        $q = "select * from solicitudes where elementoID='$elementoID' and cRID=1 and fecha=\"$fecha\" ORDER BY solicitudID asc";
        $r = mysqli_query($con, $q);
        $existentes = mysqli_num_rows($r);
        if($existentes > 0){
            echo "<table class='table'>\n";
            echo "\t<thead>\n";
            echo "\t\t<tr>\n";
            echo "\t\t\t<th scope='col'> Usuario </th>\n";
            echo "\t\t\t<th scope='col'> Entrada </th>\n";
            echo "\t\t\t<th scope='col'> Salida </th>\n";
            echo "\t\t\t<th scope='col'>  </th>\n";
            echo "\t\t\t<th scope='col'>  </th>\n";
            echo "\t\t\t<th scope='col'>  </th>\n";
            echo "\t\t</tr>\n";
            echo "\t</thead>\n";
            echo "\t<tbody>\n";
            while($columna = mysqli_fetch_array($r)){
                $carnet = $columna['carnet'];
                $entrada = $columna['entrada'];
                $salida = $columna['salida'];

                $impresion = "\t<tr>\n";
                $impresion .= "\t\t<td scope='row'>$carnet</td>\n";
                $impresion .= "\t\t<td>$entrada</td>\n";
                $impresion .= "\t\t<td>$salida</td>\n";
                $impresion .= "\t</tr>\n";

                echo $impresion;
            }
            echo "\t</tbody>\n";
            echo "</table>\n";
        }
        else{
            echo "No hay reservas en cola para este cubículo";
        }
    }

    //obtiene el estado del elemento
    function obtenerEstado($elementoID){
        //se utiliza la conexión
        $con = conectar();

        //sentencia join para obtener el estado del elemento
        $q = "select E.descripcion from elementos C inner join estados E on C.estadoID = E.estadoID where elementoID='$elementoID'";
        $r =  mysqli_query($con, $q);
        while($columna = mysqli_fetch_array($r))
        {
            $estado = $columna['descripcion'];
        }
        return $estado;
    }

    //evalua el estado e imprime con el color correspondiente
    function imprimirEstado($elementoID){
        //se obtiene el estado
        $estado = obtenerEstado($elementoID);

        //se evalua
        if($estado == "DISPONIBLE")
            $impresion = "<h5 style='color:green'>" . $estado . "</h5>";
        elseif($estado == "EN USO")
            $impresion = "<h5 style='color:red'>" . $estado . "</h5>";
        elseif($estado == "RESERVADO")
            $impresion = "<h5 style='color:blue'>" . $estado . "</h5>";
        else
            $impresion = "<h5 style='color:orange'>" . $estado . "</h5>";
        return $impresion;
    }

    function usuarioActivo($elementoID, $fecha){
        $con = conectar();
        //se inicializa la impresión
        $usuario = "No hay usuario activo en este cubículo<br><br>\n";
        
        $q = "select * from solicitudes where elementoID='$elementoID' and fecha='$fecha' ORDER BY solicitudID ASC limit 1  ";
            
        //se almacena el resultado
        $r =  mysqli_query($con, $q);

        //se evalua la cantidad de resultados (reservas para este cubiculo)
        if(mysqli_num_rows($r) > 0){
            //mientras existan registros se crea una matriz para almacenar los campos en variables
            while($columna = mysqli_fetch_array($r)){
                $usuario = $columna['carnet'];
            }
        }
        return $usuario;
    }

    //obtiene los datos del usuario activo del cubículo correspondiente
    function impresionUsuarioActivo($elementoID, $fecha){
        $con = conectar();
        //se inicializa la impresión
        $impresion = "No hay usuario activo en este cubículo<br><br>\n";
        
        $estado = obtenerEstado($elementoID);
        if($estado == "EN USO"){
            $q = "select * from registros where elementoID='$elementoID' and fecha='$fecha' order by registroID desc limit 1";
                
            //se almacena el resultado
            $r =  mysqli_query($con, $q);

            //se evalua la cantidad de resultados (reservas para este cubiculo)
            if(mysqli_num_rows($r) > 0){
                //mientras existan registros se crea una matriz para almacenar los campos en variables
                while($columna = mysqli_fetch_array($r)){
                    $usuario = $columna['carnetUsuario'];
                    $entrada = $columna['entrada'];
                    $salida = $columna['salida'];
                    $autorizacion = $columna['autorizacion'];
                }
                //se formatea la impresión
                $impresion = "<span><strong>Usuario:&nbsp;&nbsp;</strong></span>" . $usuario . "&nbsp;&nbsp;&nbsp;&nbsp;\n";
                $impresion .= "<span><strong>Entrada:&nbsp;&nbsp;</strong></span>" . $entrada . "&nbsp;&nbsp;&nbsp;&nbsp;\n";
                $impresion .= "<span><strong>Salida:&nbsp;&nbsp;</strong></span>" . $salida . "&nbsp;&nbsp;&nbsp;&nbsp;\n";
                $impresion .= "<span><strong>Autorización:&nbsp;&nbsp;</strong></span>" . $autorizacion . "&nbsp;&nbsp;&nbsp;&nbsp;\n";
            }
        }
        elseif($estado == "EN ESPERA DE AUTORIZACIÓN" || $estado = "DISPONIBLE"){
            $q = "select * from solicitudes where elementoID='$elementoID' and fecha='$fecha' and cRID=2";
                
            //se almacena el resultado
            $r =  mysqli_query($con, $q);

            //se evalua la cantidad de resultados (reservas para este cubiculo)
            if(mysqli_num_rows($r) > 0){
                //mientras existan registros se crea una matriz para almacenar los campos en variables
                while($columna = mysqli_fetch_array($r)){
                    $usuario = $columna['carnet'];
                    $entrada = $columna['entrada'];
                    $salida = $columna['salida'];
                }
                //se formatea la impresión
                $impresion = "<span><strong>Usuario:&nbsp;&nbsp;</strong></span>" . $usuario . "&nbsp;&nbsp;&nbsp;&nbsp;\n";
                $impresion .= "<span><strong>Entrada:&nbsp;&nbsp;</strong></span>" . $entrada . "&nbsp;&nbsp;&nbsp;&nbsp;\n";
                $impresion .= "<span><strong>Salida:&nbsp;&nbsp;</strong></span>" . $salida . "&nbsp;&nbsp;&nbsp;&nbsp;\n";
            }
        }

        if($impresion == "No hay usuario activo en este cubículo<br><br>\n"){
            actualizarEstado($elementoID, 1);
        }
        
        return $impresion;
    }

    //se habilita o deshabilita el botón SOLICITAR según el estado del cubículo
    function botonSolicitar($elementoID){
        //se obtiene el estado
        $estado = obtenerEstado($elementoID);

        //se evalua
        if($estado == "DISPONIBLE"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' data-target='#solicitar$elementoID'>Solicitar</button>\n";
        }
        elseif($estado == "INHABILITADO" || $estado == "EN USO" || $estado == "EN ESPERA DE AUTORIZACIÓN"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' style='background-color:grey'>Solicitar</button>\n";
        }
        return $impresion;
    }

    //se habilita o deshabilita el botón RESERVAR según el estado del cubículo
    function botonReservar($elementoID){
        //se obtiene el estado
        $estado = obtenerEstado($elementoID);

        //se evalua
        if($estado == "EN USO"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' data-target='#reservar$elementoID'>Reservar</button>\n";
        }
        elseif($estado == "INHABILITADO" || $estado == "DISPONIBLE" || $estado == "EN ESPERA DE AUTORIZACIÓN"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' style='background-color:grey'>Reservar</button>\n";
        }
        return $impresion;
    }

    //se habilita o deshabilita el botón SOLICITAR según el estado del cubículo
    function botonAutorizar($elementoID){
        //se obtiene el estado
        $estado = obtenerEstado($elementoID);

        //se evalua
        if($estado == "EN ESPERA DE AUTORIZACIÓN"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' data-target='#autorizar$elementoID'>Autorizar</button>\n";
        }
        elseif($estado == "DISPONIBLE" || $estado == "INHABILITADO" || $estado = "EN USO"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' style='background-color:grey'>Autorizar</button>\n";
        }
        return $impresion;
    }

    //se habilita o deshabilita el botón RESERVAR según el estado del cubículo
    function botonLiberar($elementoID){
        //se obtiene el estado
        $estado = obtenerEstado($elementoID);

        //se evalua
        if($estado == "EN USO"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' data-target='#liberar$elementoID'>Liberar</button>\n";
        }
        elseif($estado == "INHABILITADO" || $estado == "DISPONIBLE" || $estado == "EN ESPERA DE AUTORIZACIÓN"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' style='background-color:grey'>Liberar</button>\n";
        }
        return $impresion;
    }

    //se habilita o deshabilita el botón RESERVAR según el estado del cubículo
    function botonCancelar($elementoID){
        //se obtiene el estado
        $estado = obtenerEstado($elementoID);

        //se evalua
        if($estado == "EN ESPERA DE AUTORIZACIÓN"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' data-target='#eliminar$elementoID'>Eliminar</button>\n";
        }
        elseif($estado == "INHABILITADO" || $estado == "DISPONIBLE" || $estado == "EN USO"){
            $impresion = "<button type='button' class='btn btn-lg btn-block btn-primary' data-toggle='modal' style='background-color:grey'>Eliminar</button>\n";
        }
        return $impresion;
    }

    function obtenerSalida($entrada){
        $entradanum = strtotime($entrada);
		$tuso = date("H:i:s",7200);
		$tusonum = strtotime($tuso);
		$salidanum = $entradanum + $tusonum;									
        $salida = date("H:i:s",$salidanum);
        return $salida;
    }

    //se actualiza el estado del cubiculo segun el elemento y el nuevo estado
    function actualizarEstado($elementoID, $nuevoEstado){
        $con = conectar();
        $q = "update elementos set estadoID=$nuevoEstado where elementoID='$elementoID'";
        $r = mysqli_query($con, $q);        
    }

    //obtener salida de los registros
    function obtenerUltimaSalida($elementoID, $fecha){
        $con = conectar();
        $q = "select * from solicitudes where elementoID='$elementoID' and fecha='$fecha' order by solicitudID DESC LIMIT 1";
        $r = mysqli_query($con, $q);
        $reservas = mysqli_num_rows($r);

        if($reservas > 0){
            while($columna = mysqli_fetch_array($r)){
                $salida = $columna['salida'];
            }
        }
        else{
            $qreg = "select * from registros where elementoID='$elementoID' and fecha='$fecha' order by registroID DESC LIMIT 1";
            $rreg = mysqli_query($con, $qreg);
            $activo = mysqli_num_rows($rreg);
            if($activo > 0){
                while($columna2 = mysqli_fetch_array($rreg)){
                    $salida = $columna2['salida'];
                }
            }
            else{
                $salida = obtenerSalida(date("H:i:s"));
            }
        }
        return $salida;
    }


    //se crean las solicitudes de reserva y prestamo para ser autorizadas
    function solicitar($carnet, $password, $elementoID, $cRID){
        $con = conectar();

        //se comprueba que exista el usuario
        $qusuario = "select clave from usuarios where carnet='$carnet'";
        $rusuario = mysqli_query($con, $qusuario);
        //se almacena la cantidad de registros coincidentes
        $usuarioExistente = mysqli_num_rows($rusuario);

        //se comprueba la existencia de solicitudes del usuario para algun otro cubiculo
        $qsolicitudExistente = "select * from solicitudes where carnet='$carnet'";
        $rsolicituExistente = mysqli_query($con, $qsolicitudExistente);
        $solicitudExistente = mysqli_num_rows($rsolicituExistente);

        //si existe el usuario
        if($usuarioExistente>0){
            //si no existen solicitudes del usuario para el cubiculo
            if($solicitudExistente == 0){
                //se obtiene la clave
                while($columna = mysqli_fetch_array($rusuario)){
                    $clave = $columna['clave'];
                }
                //se evalua la coincidencia de las claves
                if(MD5($password) == $clave){
                    //se pasan los campos de tiempo del sistema
                    $fecha = date("Y-m-d");
                    if($cRID==2){
                        $entrada = date("H:i:s");
                    }
                    elseif($cRID==1){
                        $entrada = obtenerUltimaSalida($elementoID, $fecha);
                    }
                    $salida = obtenerSalida($entrada);
                    //query de inserción
                    $qsolicitar = "insert into solicitudes (carnet, fecha, entrada, salida, elementoID, cRID) values ('$carnet', '$fecha', '$entrada', '$salida', '$elementoID', $cRID)";
                    $rsolicitar = mysqli_query($con, $qsolicitar);

                    //si se inserta correctamente
                    if($rsolicitar == true){
                        if($cRID==2){
                            //se actualiza el estado del cubiculo a espera sólo si el cRID representa un prestamo (2)
                            actualizarEstado($elementoID, 5);
                        }
                        //se imprime el anuncio de exito en solicitar
                        echo "<script>$(function() { $('#solicitudIngresada').modal('show'); });</script>";
                    }
                    //en caso de error en la inserción
                    else{
                        echo "<script>$(function() { $('#errorSolicitud').modal('show'); });</script>";
                    }
                }
                else{
                    echo "<script>$(function() { $('#claveIncorrecta').modal('show'); });</script>";
                }
            }
            //si existe una solicitud del usuario para algun otro cubiculo
            else{
                echo "<script>$(function() { $('#solicitudExistente').modal('show'); });</script>";
            }
        }
        //si no existe el usuario
        else{
            echo "<script>$(function() { $('#usuarioNoEncontrado').modal('show'); });</script>";
        }
        
    }

    //se crean los prestamos en la tabla registros
    function autorizar($carnet, $password, $elementoID, $cRID){
        $con = conectar();

        //verificacion de credenciales
        //se comprueba que exista el usuario
        $qu = "select clave from usuarios where carnet='$carnet'";
        $rusuario = mysqli_query($con, $qu);
        //se almacena la cantidad de registros coincidentes
        $usuarioExistente = mysqli_num_rows($rusuario);

        //si existe el usuario
        if($usuarioExistente>0){
            //se obtiene la clave
            while($columna = mysqli_fetch_array($rusuario)){
                $clave = $columna['clave'];
            }
            //se evalua la coincidencia de las claves
            if(MD5($password) == $clave){
                // termina verificacion de credenciales
                
                //se pasan los campos de tiempo del sistema
                $fecha = date("Y-m-d");

                //se encuentra la solicitud
                $qs = "select * from solicitudes where elementoID='$elementoID'and fecha='$fecha' and cRID=2 order by solicitudID ASC limit 1";
                $rs = mysqli_query($con, $qs);
                $exis = mysqli_num_rows($rs);

                //se capturan los datos de la solicitud
                while($coluser = mysqli_fetch_array($rs)){
                    $carnetUsuario = $coluser['carnet'];
                }
                $entrada = date("H:i:s");
                $salida = obtenerSalida($entrada);
                //se crea el registro
                $qsolicitar = "insert into registros (carnetUsuario, fecha, entrada, salida, autorizacion, cRID, elementoID) values ('$carnetUsuario', '$fecha', '$entrada', '$salida', '$carnet', $cRID, '$elementoID')";
                $rsolicitar = mysqli_query($con, $qsolicitar);

                //si se inserta correctamente
                if($rsolicitar == true){
                    //se actualiza el estado del cubiculo a en uso
                    actualizarEstado($elementoID, 4);

                    //se elimina la solicitud de prestamo
                    eliminarSolicitud($elementoID, 2);

                    //se imprime el anuncio de exito en solicitar
                    echo "<script>$(function() { $('#usoAutorizado').modal('show'); });</script>";
                }
                //en caso de error en la inserción
                else{
                    echo "<script>$(function() { $('#errorSolicitud').modal('show'); });</script>";
                }
            }
        }
        //si no existe el usuario
        else{
            echo "<script>$(function() { $('#usuarioNoEncontrado').modal('show'); });</script>";
        }
    }

    //esta funcion ingresa a solicitud de autorización la reserva en cola para el cubiculo en especifico
    function actualizarcRID($solicitudID, $cRID){
        $con = conectar();
        $q = "update solicitudes set cRID=$cRID where solicitudID=$solicitudID";
        $r = mysqli_query($con, $q);
        return $r;
    }

    function liberar($elementoID, $cRID){
        //se evalua la cantidad de resultados (reservas para este cubiculo)
        $con = conectar();
        $q = "select * from solicitudes where elementoID='$elementoID' and cRID=$cRID  order by solicitudID ASC LIMIT 1";
        $r = mysqli_query($con, $q);
        $reservas = mysqli_num_rows($r);
        if($reservas > 0){
            while($columna = mysqli_fetch_array($r)){
                $solicitudID = $columna['solicitudID'];
                actualizarcRID($solicitudID, 2);
                actualizarEstado($elementoID, 5);
            }
        }
        else{
            actualizarEstado($elementoID, 1);
            eliminarSolicitud($elementoID, 2);
        }
    }

    function eliminar($elementoID, $cRID){
        //se evalua la cantidad de resultados (reservas para este cubiculo)
        $con = conectar();
        $q = "select * from solicitudes where elementoID='$elementoID' order by solicitudID asc LIMIT 1";
        $r = mysqli_query($con, $q);
        $reservas = mysqli_num_rows($r);
        if($reservas > 0){
            while($columna = mysqli_fetch_array($r)){
                $solicitudID = $columna['solicitudID'];
            }
            mysqli_query($con, "delete from solicitudes where solicitudID='$solicitudID'");
            $sigSolicitud = $solicitudID + 1;
            mysqli_query($con, "update solicitudes set cRID=2 where solicitudID='$sigSolicitud'");
        }
    }

    function eliminarSolicitud($elementoID, $cRID){
        $con = conectar();
        $q = "delete from solicitudes where elementoID='$elementoID' and cRID='$cRID'";
        $r = mysqli_query($con, $q);
    }

    function ingresar($carnet, $password){
        $con = conectar();
			
        $q = "select * from usuarios where carnet='$carnet'";
        $r = mysqli_query($con, $q);
        $existentes = mysqli_num_rows($r);
        if($existentes>0){
            while($columna = mysqli_fetch_array($r)){
                $pass = $columna['clave'];
            }
            
            if(MD5($password) == $pass){
                header('Location: solicitudes1raPlanta.php');
            }
            else{
                echo "<script>$(function() { $('#contraseñaIncorrecta').modal('show'); });</script>";
            }
        }
        else{
            echo "<script>$(function() { $('#usuarioNoEncontrado').modal('show'); });</script>";
        }
    }

    //impresión del modal para Solicitar cubículo
    function impresionModalSolicitar($elementoID){
        $server = $_SERVER['PHP_SELF'];
        $id = rand();
        $impresion = "\t<div class='modal-dialog' role='document'>\n";
        $impresion .= "\t<div class='modal-content'>\n";
        $impresion .= "\t\t<div class='modal-header'>\n";
        $impresion .= "\t\t<h5 class='modal-title' id='solicitarLabel'>Indentifícate para solicitarlo</h5>\n";
        $impresion .= "\t\t<button type='button' class='close' data-dismiss='modal' aria-label='Close'>\n";
        $impresion .= "\t\t<span aria-hidden='true'>&times;</span>\n";
        $impresion .= "\t\t</button>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-body'>\n";
        $impresion .= "\t\t<form action='$server' method='POST' autocomplete='off' id='$id'>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<label for='recipient-name' class='col-form-label'>Carnet</label>\n";
        $impresion .= "\t\t<input type='text' class='form-control' id='recipient-name' name='carnet' required>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<label for='message-text' class='col-form-label'>Contraseña</label>\n";
        $impresion .= "\t\t<input type='password' class='form-control' id='inputPassword' name='contra' required>\n";
        $impresion .= "\t\t<div class='text-center'>\n";
        $impresion .= "\t\t<a href='#'>Olvidé mi contraseña</a>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<input type='hidden' class='form-control' id='recipient-name' name='cubiculo' value='$elementoID'>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-footer'>\n";
        $impresion .= "\t\t<input type='submit' class='btn btn-success' name='solicitar' value='Solicitar'/>\n";
        $impresion .= "\t\t<input type='button' class='btn btn-danger' data-dismiss='modal' name='cancelar' value='Cancelar'/>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t</form>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        return $impresion;
    }

    //impresión del modal para reservar cubículo
    function impresionModalReservar($elementoID){
        $server = $_SERVER['PHP_SELF'];
        $id = rand();
        $impresion = "\t<div class='modal-dialog' role='document'>\n";
        $impresion .= "\t<div class='modal-content'>\n";
        $impresion .= "\t\t<div class='modal-header'>\n";
        $impresion .= "\t\t<h5 class='modal-title' id='reservarLabel'>Indentifícate para reservarlo</h5>\n";
        $impresion .= "\t\t<button type='button' class='close' data-dismiss='modal' aria-label='Close'>\n";
        $impresion .= "\t\t<span aria-hidden='true'>&times;</span>\n";
        $impresion .= "\t\t</button>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-body'>\n";
        $impresion .= "\t\t<form action='$server' method='POST' autocomplete='off' id='$id'>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<label for='recipient-name' class='col-form-label'>Carnet</label>\n";
        $impresion .= "\t\t<input type='text' class='form-control' id='recipient-name' name='carnet' required>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<label for='message-text' class='col-form-label'>Contraseña</label>\n";
        $impresion .= "\t\t<input type='password' class='form-control' id='inputPassword' name='contra' required>\n";
        $impresion .= "\t\t<div class='text-center'>\n";
        $impresion .= "\t\t<a href='#'>Olvidé mi contraseña</a>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<input type='hidden' class='form-control' id='recipient-name' name='cubiculo' value='$elementoID'>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-footer'>\n";
        $impresion .= "\t\t<input type='submit' class='btn btn-success' name='reservar' value='Reservar'/>\n";
        $impresion .= "\t\t<input type='button' class='btn btn-danger' data-dismiss='modal' name='cancelar' value='Cancelar'/>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t</form>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        return $impresion;
    }

    //impresión del login
    function impresionLogin(){
        $server = $_SERVER['PHP_SELF'];
        $id = rand();
        $impresion = "\t<div class='modal-dialog' role='document'>\n";
        $impresion .= "\t<div class='modal-content'>\n";
        $impresion .= "\t\t<div class='modal-header'>\n";
        $impresion .= "\t\t<h5 class='modal-title' id='reservarLabel'>Ingrese sus credenciales de biblioteca</h5>\n";
        $impresion .= "\t\t<button type='button' class='close' data-dismiss='modal' aria-label='Close'>\n";
        $impresion .= "\t\t<span aria-hidden='true'>&times;</span>\n";
        $impresion .= "\t\t</button>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-body'>\n";
        $impresion .= "\t\t<form action='$server' method='POST' autocomplete='off' id='$id'>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<label for='recipient-name' class='col-form-label'>Carnet</label>\n";
        $impresion .= "\t\t<input type='text' class='form-control' id='recipient-name' name='carnet' required>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<label for='message-text' class='col-form-label'>Contraseña</label>\n";
        $impresion .= "\t\t<input type='password' class='form-control' id='inputPassword' name='contra' required>\n";
        $impresion .= "\t\t<div class='text-center'>\n";
        $impresion .= "\t\t<a href='#'>Olvidé mi contraseña</a>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-footer'>\n";
        $impresion .= "\t\t<input type='submit' class='btn btn-success' name='ingresar' value='Ingresar'/>\n";
        $impresion .= "\t\t<input type='button' class='btn btn-danger' data-dismiss='modal' name='cancelar' value='Cancelar'/>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t</form>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        return $impresion;
    }

     //impresión del modal para autorizar cubículo
     function impresionModalAutorizar($elementoID){
        $server = $_SERVER['PHP_SELF'];
        $id = rand();
        $impresion = "\t<div class='modal-dialog' role='document'>\n";
        $impresion .= "\t<div class='modal-content'>\n";
        $impresion .= "\t\t<div class='modal-header'>\n";
        $impresion .= "\t\t<h5 class='modal-title' id='solicitarLabel'>Ingrese sus credenciales para autorizarlo</h5>\n";
        $impresion .= "\t\t<button type='button' class='close' data-dismiss='modal' aria-label='Close'>\n";
        $impresion .= "\t\t<span aria-hidden='true'>&times;</span>\n";
        $impresion .= "\t\t</button>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-body'>\n";
        $impresion .= "\t\t<form action='$server' method='POST' autocomplete='off' id='$id'>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<label for='recipient-name' class='col-form-label'>Carnet</label>\n";
        $impresion .= "\t\t<input type='text' class='form-control' id='recipient-name' name='carnet' required>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<label for='message-text' class='col-form-label'>Contraseña</label>\n";
        $impresion .= "\t\t<input type='password' class='form-control' id='inputPassword' name='contra' required>\n";
        $impresion .= "\t\t<div class='text-center'>\n";
        $impresion .= "\t\t<a href='#'>Olvidé mi contraseña</a>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='form-group'>\n";
        $impresion .= "\t\t<input type='hidden' class='form-control' id='recipient-name' name='cubiculo' value='$elementoID'>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-footer'>\n";
        $impresion .= "\t\t<input type='submit' class='btn btn-success' name='autorizar' value='Autorizar'/>\n";
        $impresion .= "\t\t<input type='button' class='btn btn-danger' data-dismiss='modal' name='cancelar' value='Cancelar'/>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t</form>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        return $impresion;
    }

    //impresión del modal para liberar cubículo
    function impresionModalLiberar($elementoID){
        $id = rand();
        $server = $_SERVER['PHP_SELF'];
        $impresion = "\t<div class='modal-dialog' role='document'>\n";
        $impresion .= "\t<div class='modal-content'>\n";
        $impresion .= "\t\t<div class='modal-header'>\n";
        $impresion .= "\t\t<h5 class='modal-title' id='reservarLabel'>Confirme la liberación del cubículo</h5>\n";
        $impresion .= "\t\t<button type='button' class='close' data-dismiss='modal' aria-label='Close'>\n";
        $impresion .= "\t\t<span aria-hidden='true'>&times;</span>\n";
        $impresion .= "\t\t</button>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-body'>\n";
        $impresion .= "\t\t<form action='$server' method='POST' autocomplete='off' id='$id' >\n";
        $impresion .= "\t\t<input type='hidden' class='form-control' id='recipient-name' name='cubiculo' value='$elementoID'>\n";
        $impresion .= "\t\t<center><input type='submit' class='btn btn-success' name='liberar' value='Liberar'/>\n";
        $impresion .= "\t\t<input type='button' class='btn btn-danger' data-dismiss='modal' name='cancelar' value='Cancelar'/></center>\n";
        $impresion .= "\t\t</form>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        return $impresion;
    }

    //impresión del modal para liberar cubículo
    function impresionModalEliminar($elementoID){
        $id = rand();
        $server = $_SERVER['PHP_SELF'];
        $impresion = "\t<div class='modal-dialog' role='document'>\n";
        $impresion .= "\t<div class='modal-content'>\n";
        $impresion .= "\t\t<div class='modal-header'>\n";
        $impresion .= "\t\t<h5 class='modal-title' id='reservarLabel'>Confirme la eliminación de la reserva</h5>\n";
        $impresion .= "\t\t<button type='button' class='close' data-dismiss='modal' aria-label='Close'>\n";
        $impresion .= "\t\t<span aria-hidden='true'>&times;</span>\n";
        $impresion .= "\t\t</button>\n";
        $impresion .= "\t\t</div>\n";
        $impresion .= "\t\t<div class='modal-body'>\n";
        $impresion .= "\t\t<form action='$server' method='POST' autocomplete='off' id='$id' >\n";
        $impresion .= "\t\t<input type='hidden' class='form-control' id='recipient-name' name='cubiculo' value='$elementoID'>\n";
        $impresion .= "\t\t<center><input type='submit' class='btn btn-success' name='eliminar' value='Eliminar'/>\n";
        $impresion .= "\t\t<input type='button' class='btn btn-danger' data-dismiss='modal' name='cancelar' value='Cancelar'/></center>\n";
        $impresion .= "\t\t</form>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        $impresion .= "\t</div>\n";
        return $impresion;
    }
?>