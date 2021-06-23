<?php
//rutas de carpetas de la aplicacion
const DirBack = "../";
const DirApp = DirBack."app/";
const DirControllers = "Controllers/";
const DirModels = "Models/";
const DirViews = "Views/";
const DirFramework = "Framework/";
const php = ".php";
const html = ".html";

const BASE_SETTING = [
    "base_url"=>"http://localhost/framework"
];

/* configuracion de FTP */
const FTP_SETTING =[
    "folder_base"=>"public", // carpeta base de host, public_html/
    "server"=>"localhost", // servidor FTP
    "port"=>"21", // puerto de coneccion default 21
    "user"=>"root", // usuario de acceso
    "password"=>"12345" // contraseña
];

/* configuración de conección a la base de datos */
const BD_SETTING = [
    "connector"=>"mysql",//mysql,pgsql,sqlserver
    "server"=>"localhost",//nombre o ip de servidor de BD
    "port"=>"3306",// puerto
    "database"=>"basededatos",// base de datos de aplicación
    "user"=>"usuario", // usuario de acceso a BD
    "password"=>"clave", //contraseña de acceso a BD
    "charset"=>"utf8" // encodificación de conección
];

/* headers */
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Headers: *");
header('Access-Control-Allow-Methods: *');

?>