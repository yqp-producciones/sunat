<?php
use Peru\Sunat\RucFactory;
use Peru\Jne\DniFactory;
class ConsultaController extends Controller
{
    
    public function __construct(){ }
    
    public function ruc($ruc=''){
        if($ruc != ''){
            /* opcion 01 => usar libreria giansalex */
            require_once '../app/Framework/vendor/autoload.php';
            $factory = new RucFactory();
            $cs = $factory->create();
            $empresa = (array)$cs->get($ruc);
            if ($empresa != null) {
                $retorno = [
                    'documento'=>$ruc,
                    'nombre'=>$empresa['razonSocial'],
                    'direccion'=>$empresa['direccion'],
                    'tipo_documento'=>6,
                    'estado'=>$empresa['estado'],
                    'condicion'=>$empresa['condicion'],
                    'ubigeo'=>$empresa['ubigeo']
                ];
                echo json_encode(['result'=>1,'message'=>'Exitoso','data'=>$retorno],true);
            }else{
                /* opcion 2 => usar api rest de apis.net.pe */
                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api.apis.net.pe/v1/ruc?numero=' . $ruc,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_HTTPHEADER => array(
                    'Referer: http://apis.net.pe/api-ruc',
                    'Authorization: Bearer apis-token-421.3mH36YOYYtbbEv9qko1V1VefBoFDhsD-'
                    ),
                ));
                $response = curl_exec($curl);
                curl_close($curl);
                $empresa = (object)json_decode($response);
                if(!isset($empresa->error)){
                    $retorno = [
                        'documento'=>$ruc,
                        'nombre'=>$empresa->nombre,
                        'direccion'=>$empresa->direccion,
                        'tipo_documento'=>6,
                        'estado'=>$empresa->estado,
                        'condicion'=>$empresa->condicion
                    ];
                    echo json_encode(['result'=>1,'data'=>$retorno,'message'=>'Exitoso']);
                } else {
                    echo json_encode(['result'=>0,'data'=>null,'message'=>'Número de documento no existe o es inválido']);
                }
            }
        } else {
            echo json_encode(['result'=>0,'data'=>null,'message'=>'Tienes que ingresar el numero de ruc']);
        }   
    }
    
    public function dni($dni=''){
        if(strlen($dni) == 8){
            require_once '../app/Framework/vendor/autoload.php';
            /*verifica reniec*/
            $factory = new DniFactory();
            $cs = $factory->create();
            $person = (array)$cs->get($dni);
            if($person != null){
                $retorno = [
                    'documento'=>$dni,
                    'nombre'=>$person['nombres'].' '.$person['apellidoPaterno'].' '.$person['apellidoMaterno'],
                    'codigo'=>$person['codVerifica'],
                    'tipo_documento'=>1
                ];
                echo json_encode(['result'=>1,'message'=>'Exitoso','data'=>$retorno],true);
            } else {
                echo json_encode(['result'=>0,'message'=>'Número de documento no existe o es inválido','data'=>null]);
            }
        } else {
            echo json_encode(['result'=>0,'message'=>'Longitud de numero no es valido para DNI','data'=>null]);
        }
    }
}
