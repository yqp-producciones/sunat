<?php
class Modelo extends Base
{
    public function __construct(){
        $this->onInit();
    }
    public function selects($data=null){
        try {
            $result;
            switch ($data['op']) {
                case 'sel-fill': 
                    $this->query("select *, concat('".BASE_SETTING['base_url']."',imagen) as url from galeria");
                    $result = $this->resultset();
                    break;
                case 'sel-autoincrement':
                    $this->query("SELECT auto_increment as nextval FROM INFORMATION_SCHEMA.TABLES WHERE table_name = 'tabla'");
                    $result = $this->resultset();
                    break;
                default: return $this->invalid_option();
            }
        } catch (\Exception $ex) { $result = new DbpResponse([],false,0,$ex->getMessage(),'Error interno de sistema'); }
        return $result;
    }

    public function inserts($data){
        $result;
        try {
            switch (getString($data,'op','ins-default')) {
                case 'ins-default':
                    $this->query("insert into tabla (column1,column2) values (:column1,:column2)");
                    $this->bind(':column1',getString($data,'column1',null));
                    $this->bind(':column2',getString($data,'column2',null));
                    $a = $this->execute();
                    if($a->result){ $result = new DbpResponse([],true,1,'Nuevo elemento registrado conrrectamente.','Registro exitoso'); } 
                    else { $result = new DbpResponse([],false,0,$a->message,$a->title);}
                break;
                default: return $this->invalid_option();
            }
        } catch (\Exception $ex) { $result = new DbpResponse([],false,0,$ex->getMessage(),'Error al registrar elemento'); }
        return $result;
    }

    public function updates($data){
        $result;
        try {
            switch ($this->getValue($data,'op','udp-default')) {
                case 'udp-default':
                    $this->query("update tabla set column1=:column1,column2=:column2 where id =:id");
                    $this->bind(':id',getString($data,'id',null));
                    $this->bind(':column1',getString($data,'column1',null));
                    $this->bind(':column2',getString($data,'column2',null));
                    $a = $this->execute();
                    if($a->result){ $result = new DbpResponse([],true,1,'Datos  guardados correctamente.','Datos modificados'); } 
                    else { $result = new DbpResponse([],false,0,$a->message,$a->title);}
                break;
                default: return $this->invalid_option();
            }
        } catch (\Exception $ex) { $result = new DbpResponse([],false,0,$ex->getMessage(),'Error al guardar elemento'); }
        return $result;
    }

    public function deletes($data){
        $result;
        try {
            switch ((isset($data['op']) ? $data['op'] :'del-default')) {
                case 'del-default':
                    $this->query("delete from tabla where id= :id");
                    $this->bind(':id',getString($data,'id',null));
                    $a = $this->execute();
                    if($a->result){$result = new DbpResponse([],true,1,'dato eliminado del sistema.','elemento eliminado');
                    } else { $result = new DbpResponse([],false,0,$a->message,$a->title);}
                break;
                default: return $this->invalid_option();
            }
        } catch (\Exception $ex) { return $this->response($ex->getMessage(),0);} 
        return $result;
    }
}
?>