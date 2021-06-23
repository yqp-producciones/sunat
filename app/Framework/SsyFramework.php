<?php
class Core
{
    protected $controller = "IndexController"; //default
    protected $method = "index"; //default
    protected $parameters = []; //default
    public function __construct(){
        $url = $this->getUrl();

        /* CONFIGURACION DE CONTROLADOR */
        //verficar si existe el controlador o el archivo del controlador
        if(file_exists(DirApp.DirControllers.ucwords($url[0]).'Controller'.php)){
            $this->controller = ucwords($url[0]).'Controller';
            unset($url[0]);
        }
        require_once DirApp.DirControllers.$this->controller.php;
        $this->controller = new $this->controller;
        
        /* CONFIGURACION DE METODO */
        //verficar si existe metodo
        if(isset($url[1])){
            if(method_exists($this->controller,$url[1])){
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        /* CONFIGURACION DE PARAMETROS */
        $this->parameters= $url ? array_values($url):[];
        
        /* INVOCAR LA FUNCION */
        call_user_func_array([$this->controller,$this->method],$this->parameters);
    }

    //devuelve un array ordenado de la url
    public function getUrl(){
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'],'/');
            $url = filter_var($url,FILTER_SANITIZE_URL);
            $url = explode('/',$url);
            return $url;
        }
    }
}
class Controller
{
    public $current=[];
    public function view($view, $data=[]){
       // echo DirApp.DirViews.$view.php;
        if(file_exists(DirApp.DirViews.$view.'/'.str_replace('/','.',$view).php)){
            require_once DirApp.DirViews.$view.'/'.str_replace('/','.',$view).php;
            includeJS($view.'/'.str_replace('/','.',$view));
        }
        else
        {
            echo  DirApp.DirViews.$view.'/'.str_replace('/','.',$view).'<br>';
            die(' no existe vista ');
        }
    }
    public function json($array){
        return json_encode($array);
    }
    public function redirectTo($ruta){
        print("<script> document.location.href='".SiteUrl.$ruta."';</script>");
    }
    public function getPost(){
        return json_decode(file_get_contents('php://input'), true);
    }
}
class Base
{
    public $pdo;
    public $stmt;
    public $error;
    public $is_connection;
    public function onInit(){
        $ssy_connector = new SsyConnector(BD_SETTING);
        $opcion = array( PDO::ATTR_PERSISTENT=>true, PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION );
        try
        {
            $this->pdo = new PDO($ssy_connector->GetConnectionString(),$ssy_connector->setting->user,$ssy_connector->setting->password);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->is_connection = true;
        }
        catch(PDOException $ex){
            $this->is_connection = false;
            $this->error = $ex->getMessage();
        }
    }

    //prepara el comando sql a ejecutar
    public function query($sql){
        if($this->pdo != null){
            $this->stmt = $this->pdo->prepare($sql);
        }
    }

    //asigna los parametros que se usaran en la consulta
    public function bind($parameters,$values,$tipes=null){
        if($this->pdo != null){
            if(is_null($tipes)){
                switch(true){
                    case is_int($values): $tipo = PDO::PARAM_INT; break;
                    case is_bool($values): $tipo = PDO::PARAM_BOOL; break;
                    case is_null($values): $tipo = PDO::PARAM_NULL; break;
                    default: $tipo = PDO::PARAM_STR;
                }
            }
            $this->stmt->bindvalue($parameters,$values,$tipo);
        }
    }

    //ejecuta una instruccion sql
    public function execute(){
        $result = new PdoResponse();
        if($this->is_connection){
            try
            { $result = new PdoResponse($this->stmt->execute(),'','');}
            catch(PDOException $ex) {  $result = new PdoResponse(false,$ex->getMessage(),'Error al ejecutar comando'); }
        } else { $result = new PdoResponse(false,$this->error,'Error de conección'); }
        return $result;
    }

    //retorna la lista obtenida de la consulta
    public function resultset(){
        $result = new DbpResponse([]);
        try {
            $r = $this->Execute();
            if($r->result){ $result = new DbpResponse($this->stmt->fetchALL(),true,1,$r->message,$r->title);
            } else { $result = new DbpResponse([],false,0,$r->message,$r->title); }
        } catch (\Exception $ex) { $result = new DbpResponse([],false,0,$ex->getMessage(),'Error al obtener consulta'); }
        return $result;
    }

    public function invalid_option(){
        return  new DbpResponse([],false,0,'Opcion inválida, por favor comuníquese con el desarrollador del software para mayor información','Error al obtener lista');
    }
}
class FTP {

    /* subir archivos por FTP */
    public static function upload($filename='',$folder='',$newfilename=''){
        $setting = (object)FTP_SETTING;
        $base_setting = (object)BASE_SETTING;
        $result;
        if(isset($_FILES[$filename])){
            if($_FILES[$filename]['name'] != ''){
                if(is_uploaded_file($_FILES[$filename]["tmp_name"])){
                    $cid = ftp_connect($setting->server);
                    $resultado = ftp_login($cid,$setting->user,$setting->password);
                    ftp_pasv($cid,true);
                    if ((!$cid) || (!$resultado)) {
                        $result = new DbpResponse([],false,0,'No se pudo conectar con el servidor FTP o sus credenciales son incorrectas','Error acceder servidor FTP');
                    } else {
                        $a = explode("/",$folder);
                        if(@ftp_chdir($cid,$setting->folder_base)){
                            $path=$setting->folder_base;
                            if(is_array($a)){
                                for ($i=0; $i < count($a); $i++) { 
                                    if (@ftp_chdir($cid,$a[$i]) == false) { 
                                        ftp_mkdir($cid, $a[$i]);
                                        @ftp_chdir($cid, $a[$i]);
                                    }
                                }
                            } else {
                                if (@ftp_chdir($cid,$folder) == false) {
                                    ftp_mkdir($cid, $folder);
                                    @ftp_chdir($cid,$folder);
                                } 
                            }
                            $n = $_FILES[$filename]["name"];
                            if(strpos($n,'.') != false){
                                $d = explode('.',$n);
                                $m = $newfilename.'.'.$d[1];
                                if(@ftp_put($cid,$m,$_FILES[$filename]["tmp_name"],FTP_BINARY)){
                                        $result = new DbpResponse(['url'=>$base_setting->base_url. @ftp_pwd($cid).'/'.$m,'url_parcial'=>@ftp_pwd($cid).'/'.$m],true,1,'El archivo fue subido correctamente. Gracias','Archivo subido con exito');
                                } else { 
                                    $result = new DbpResponse([],true,1,'No se pudo subir el archivo al servidor. Gracias','Error al subir archivo');
                                }
                            }
                        } else {$result = new DbpResponse([],false,0,'No se pudo acceder a la carpeta base del servidor FTP','No acceso a base');}
                        ftp_close($cid);
                    }
                }
                else{$result = new DbpResponse([],false,0,'Posible ataque o archivo dañado','archivo no cargado');}
            } else { $result = new DbpResponse([],false,0,'No se ha cargado ningun archivo','archivo no cargado');}
        } else { $result = new DbpResponse([],false,0,'El indice '.$filename.' de array $_Files[] es nulo. por favor intenta nuevamente.','Error de índice');}
        return $result;
    }
    public static function delete($url =''){
        $result;
        $setting = (object)FTP_SETTING;
        $base_setting = (object)BASE_SETTING;
        $cid = ftp_connect($setting->server);
        $resultado = ftp_login($cid,$setting->user,$setting->password);
        ftp_pasv($cid,true);
        if ((!$cid) || (!$resultado)) {
            $result = new DbpResponse([],false,0,'No se pudo conectar con el servidor FTP o sus credenciales son incorrectas','Error acceder servidor FTP');
        } else {
            $new_url = str_replace($base_setting->base_url,'',$url);
            if(ftp_delete($cid,$new_url)){
                ftp_close($cid);
                $result = new DbpResponse([],true,1,'Archivo eliminado de servidor FTP','Archivo eliminado');
            }else{
                ftp_close($cid);
                $result = new DbpResponse([],false,0,'No se pudo eliminar el archivo FTP','error al eliminar');
            }
        }
        return $result;
    }

}
class SsyConnector {
    public $setting;
    public function __construct($_setting=[]) {
        $this->setting = (object)$_setting;
    }
    public function GetConnectionString(){
        switch ($this->setting->connector) {
            case 'mysql':
                return sprintf("mysql:host=%s;port=%d;dbname=%s;charset=%s",$this->setting->server,$this->setting->port,$this->setting->database,$this->setting->charset);
            case 'pgsql':
                return sprintf("pgsql:host=%s;port=%d;dbname=%s;charset=%s",$this->setting->server,$this->setting->port,$this->setting->database,$this->setting->charset);
            default: return "";
        }
    }
}
class PdoResponse{
    public $data;
    public $result;
    public $message;
    public $title;
    public function __construct($_result=true,$_message='',$_title=''){
        $this->result = $_result;
        $this->message = $_message;
        $this->title = $_title;
    }
}
class DbpResponse {
    public $data;
    public $result;
    public $state;
    public $message;
    public $title;
    public function __construct($_data,$_result=true,$_state=1,$_message='',$_title=''){
        $this->data = $_data;
        $this->result = $_result;
        $this->state = $_state;
        $this->message = $_message;
        $this->title = $_title;
    }
}

//funciones generales de uso
/* obtiene el primer elemento de un array*/
function getFirt($array= null){
    $r = null;
    foreach ($array as $row) { $r = $row;break; }
    return $r;
}
function getString($array=[],$indice,$default=''){
    $result = $default;
    if(isset($array)){
        try {
            $result = $array[$indice];
        } catch (\Exception $ex) { }
    }
    return $result;
}
function getInt($array=[],$indice,$default=0){
    $result = $default;
    if(isset($array)){
        try {
            $str = getString($array,$indice,'0');
            if(is_numeric($str)){ $result = (int)$str; }
        } catch (\Exception $ex) { }
    }
    return $result;
}
function getFloat($array=[],$indice,$default=0.0){
    $result = $default;
    if(isset($array)){
        try {
            $str = getString($array,$indice,'0');
            if(is_numeric($str)){ $result = (float)$str; }
        } catch (\Exception $ex) { }
    }
    return $result;
}

function includeJS($file,$data=null){
    echo "<script class='ssy-script' type='text/javascript'>";
    include(DirApp.DirViews.$file.'.js');
    echo "</script>";
}