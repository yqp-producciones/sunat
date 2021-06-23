class SsyFramework{
    Prefijo ='';
    constructor() { 
    }
    static get BaseUrl(){return $('body').data('url');}

    inicialice(){
        $('[data-id]').each(function(){
            let e = $(this);
            let di = e.attr('data-id');
            e.removeAttr('data-id');
            e.data('idelhtml',di);
        });
    }
    Prf(_data_id){ return this.Prefijo +'-'+_data_id;}
}
class Ramdom {
    static Text(longitud){
        const characters ='ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let result1= Math.random().toString(36).substring(0,longitud);       
        return result1;
    }
}
class Component extends SsyFramework{
    #container='';
    get Container(){return this.#container;}
    set Container(_container){this.#container=_container;}
    static component;
    static html;
    prefijo='';
    controller='';
    constructor(_container_name){
        super();
        this.Container = $(_container_name);
        if(this.Container.length > 0){
            this.Container.parent().append(this.Container.html());
            this.Container.remove();
        }
        this.inicialice();
    }
}
class ErrorPrint{
    static PrintDefault(message='',title='información de SsyFramework'){
        console.log('%c ' + message,'background: #dc3545; color: #fff; border-radius:5px; padding:5px;'); 
    }
}
class Control{ 
    constructor(name){
        let elm = undefined;
        if(name.indexOf('#') != -1 || name.indexOf('.') != -1){elm = $(name);} 
        else {$(":data(idelhtml)").each(function(){if($(this).data('idelhtml') == name){elm = $(this);return false;}});}
        if(elm != undefined ? elm.length == 0 : false){ ErrorPrint.PrintDefault('no existe un elemento con el identificador: ['+name +'] revise la asignación de sus controles en las etiquetas html.');}
        return elm;
    }
}
class FormGroup {
    constructor(data_id){
        this.form = new Control(data_id);
        this.form.submit(function(e){e.preventDefault();});
    }
    get Input(){return this.form[0].elements;}

    SetString(name,value){
        if(value == undefined){value='';}
        if(this.Input[name] != undefined){ this.Input[name].value = value ; }
    }
    SetDate(name,value,format='YYYY-MM-DD'){ try { if(value != null){ this.Input[name].value = moment(value).format(format) ; } else{  this.Input[name].value = moment().format(format) ; }  } catch (error) { } }

    Clear(){
        for (let input of this.elements) {
            if(input.type != 'select-one'){
               input.value = null;
            }
        }
    }
    get GetFormData(){return new FormData(this.form[0]);}
}


class Div {constructor(_class='',_content=null){return $('<div></div>').addClass(_class).append(_content);}}
class Img {constructor(_class='',_src=''){return $('<img/>').addClass(_class).attr('src',_src);}}
class H5 {constructor(_class='',_content=''){ return $('<h5></h5>').addClass(_class).html('').append(_content);}}
class Button {constructor(_class='',_content=''){return $('<button></button>').addClass(_class).append(_content);}}
class A {constructor(_class='',_content=''){return $('<a></a>').addClass(_class).append(_content).attr('href','#');}}
class I { constructor(_class='',_content=''||[]){let elm = $('<i></i>').addClass(_class).html('').append(_content);elm.firt =elm[0]; return elm; }}
class Boostrap {
    get Col(){return {
        col_1:1,col_2:2,col_3:3,col_4,

    }};
}
class CardGroup {
    #container=undefined;
    #columns = 4;
    #cards= new Array();
    constructor(){ }
    get Items(){ return this.#cards;}
    set Items(_item){this.#cards=_item;}
    get Container(){return this.#container;}
    set Container(_html){this.#container = _html;}
    get Columns(){return this.#columns;}
    set Columns(_length){this.#columns = _length;}

    Add(_card = new Card()){
        this.Items.push(_card);
        this.Show();
    }
    Show(){
        this.Container = new Div('col-12');
        this.Container.html('');
        let cont = 1;
        let group = undefined;
        for (let i = 0; i < this.Items.length; i++) {
            let card = Card.From(this.Items[i]);
            let col = new Div('col-md-'+ (12 / this.Columns) + (cont == 1 ? ' pr-2': ((cont) == this.Columns) ? ' pl-2' : ' pl-2 pr-2' ));
            col.append(card.Elements.card);
            if(cont == 1){ group = new Div('row mb-3');}
            if(cont <= this.Columns){ group.append(col); }
            if((i+1) == this.Items.length){ this.Container.append(group); }
            if(cont == this.Columns){ this.Container.append(group);cont = 1; }
            else{ cont++; }
        }
        
    }

}

class Card {
    #card=undefined;
    #img =undefined;
    #a = undefined;
    #card_body=undefined;
    #card_title = undefined;
    #card_text = undefined;
    #title='';
    #descripcion='';
    #Image='';
    #overlay_img='';
    #button_group = new ButtonGroup();
    #buttons = undefined;
    #overlay = true;
    constructor(_title='Titutlo Tarjeta',_descripcion='este es un  modelo de tarjeta predeterminada',_image='#') {
        this.#card = new Div('card');
        this.#img = new Img('card-img-top',_image);
        this.#card_body = new Div('card-body p-2');
        this.#card_title = new H5('card-title text-center m-0 p-1 border-bottom');
        this.#card_text = new Div('card-text text-justify');
        this.#a = new A('');
        this.#button_group = new ButtonGroup();
        this.Title = _title;
        this.Show();
        $(window).resize(()=>{
            this.ImageSize();
        });
    }
    ImageSize(){
        //this.#img.css('min-height',this.#card.width());
        //this.#img.css('max-height',this.#card.width());
    }
    get Elements(){return {card:this.#card,img:this.#img,title:this.#card_title,description:this.#card_text,a:this.#a};}
    
    get Title(){return this.#title;}
    set Title(_title){ this.#title=_title; this.#card_title.html('').append(this.Title);}

    get Descripcion(){return this.#descripcion;}
    set Descripcion(_html){this.#descripcion=_html; this.#card_text.html('').append(this.Descripcion);}

    get Image(){return this.#Image};
    set Image(_link){
        this.#Image=_link;
        this.Elements.img.attr('src',_link);
        if(this.IsOverlay){
            if(this.ImageOverlay != undefined && this.ImageOverlay != ''){ this.Elements.a.attr('href',this.ImageOverlay); } 
            else { this.Elements.a.attr('href',_link); }
        }
    }

    get Buttons(){return this.#button_group;}
    set Buttons(_button_group){this.#button_group = _button_group;}

    get IsOverlay(){return this.#overlay;}; set IsOverlay(_boolean){this.#overlay = _boolean;}
    get ImageOverlay(){return this.#overlay_img;}; set ImageOverlay(_src){ this.ImageOverlay =_src;}

    Show() {
        this.#card_title.html('').append(this.Title);
        this.#card_body.html('').append([this.#button_group.Container,this.#card_title,this.#card_text]);
        if(this.IsOverlay){
            this.#a.attr('data-lighter','');
            if(this.ImageOverlay != undefined || this.ImageOverlay != ''){ this.#a.attr('href',this.ImageOverlay); } 
            else { this.#a.attr('href',this.Image); }
        }
        this.#a.append(this.#img);
        this.#card.append([this.#a,this.#card_body]);  
    }
    static From(_card = new Card()){return _card;}
}
class ButtonGroup{
    #items = [];
    #container = undefined;
    constructor() {
        this.#container = new Div('btn-group');
    }

    get Items(){return this.#items;}
    set Items(_items){this.#items = _items};

    get Container(){return this.#container;}
    set Container(_html){this.#container = _html;}

    Add(_button = new Button()){
        this.Items.push(_button);
        this.Container.append(_button);
    }
    Show(){
        this.Container.html('');
        for (let item of this.Items) {
            console.log(item);
            this.Container.append(item);
        }
    }
}

class NotifySetting {
    constructor(confirmButtonText='Aceptar',cancelButtonText='Cancelar', showCancelButton=false,html='', confirmButtonClass='btn btn-success',cancelButtonClass='btn btn-danger',buttonsStyling=false){
        this.buttonsStyling = buttonsStyling != null ? buttonsStyling : false;
        this.confirmButtonClass = confirmButtonClass != null ? confirmButtonClass : 'btn btn-success';
        this.confirmButtonText = confirmButtonText != null ? confirmButtonText : 'Aceptar';
        this.cancelButtonText = cancelButtonText != null ? cancelButtonText : 'Cancelar';
        this.cancelButtonClass = cancelButtonClass != null ? cancelButtonClass : 'btn btn-danger';
        this.showCancelButton = showCancelButton != null ? showCancelButton : false;
        this.html = html != null ? html : '';
    } 
}
class DialogResult{
    static Ok = 1;
    static Yes = 1;
    static No = 2;
    static Cancel = 3;
    static None = 0;
}
class MessageBox {
    constructor(){}
    static get Icons(){return {Error:'error',Success:'success',Info:'info',Warning:'warning',Question:'question'};}
    static get Buttons() {return {Ok:1,YesNo:2,YesNoCancel:3};}
    static async Show(message='',title ='Proceso realizado Con Éxito', messageBoxIcon = this.Icons.Success,messageBoxButton= this.Buttons.Ok){
        if(SsyConvert.ToInt(messageBoxIcon,null,-1) != -1){
            switch (SsyConvert.ToInt(messageBoxIcon,null)) {
                case 0: messageBoxIcon = this.Icons.Error; break;
                case 1: messageBoxIcon = this.Icons.Success; break;
                case 2: messageBoxIcon = this.Icons.Info; break;
                case 3: messageBoxIcon = this.Icons.Warning; break;
            }
        }
        let dialog = await Swal.fire({
            icon: messageBoxIcon,
            title: title,
            text:message,
            showDenyButton: messageBoxButton == 2 || messageBoxButton == 3,
            showCancelButton: messageBoxButton == 3,
            confirmButtonText: "<i class='fa fa-check-circle'></i>" + (messageBoxButton == 2 || messageBoxButton == 3 ? ' Continuar' : ' Aceptar'),
            cancelButtonText: "<i class='fa fa-ban'></i> Cancelar",
            denyButtonText: "<i class='fa fa-ban'></i> Cancelar",
        });
        if(dialog.isConfirmed){return DialogResult.Ok;}
        else if(dialog.isDenied){return DialogResult.No;}
        else if(dialog.isDismissed){return DialogResult.Cancel;}
        else{return DialogResult.None;}
    }

    static Wait(message='',title=''){ Swal.fire( {title: title, html: message, timerProgressBar: true,closeOnClickOutside:false,allowOutsideClick: false,didOpen: () => { Swal.showLoading() },}); }
    static Close(){ Swal.close();}
    static Error(){}
    

    /* muestra una ventana de dialogo con la informacion del error */
    error(message,title ='Error de consola'){
        this.close();
        if(message.indexOf('CD1000') >=0){ title = 'Opción Inválida'; message = 'Estimado(a), por favor, comuníquese con el desarrollador para ver la solución de este problema. Gracias.'}
        if(iserror){
            this.show(message+"",title,States.error);
        }
        else {} 
    }
    toaster(message,title ='Info!',type = 1,timeout=4000) {
	    toastr.options = {
	      closeButton: true,
	      debug: false,
	      newestOnTop: false,
	      progressBar: true,
	      positionClass: 'toast-top-right',
	      preventDuplicates: false,
	      onclick: null,
	      showDuration: "300",
	      hideDuration: "1000",
	      timeOut: timeout,
	      extendedTimeOut: "1000",
	      showEasing: "swing",
	      hideEasing: "linear",
	      showMethod: "fadeIn",
	      hideMethod: "fadeOut"
        };
        switch (type) {
            case 0:toastr.error(message,title); break;
            case 1:toastr.success(message,title); break;
            case 2:toastr.info(message,title); break;
            case 3:toastr.warning(message,title); break;
            default:  break;
        }
	    
	  }
}

class HttpClientResponse{ result = false; data = null; message = null; length = 0; title=null; type=0; constructor(_result,_data,_message,_length = 0,_title,_type=0) { this.title=_title; this.result = _result; this.data = _data; this.message = _message; this.length = _length; this.type=_type;}}
class HttpClient {
    Url = undefined;
    Body = undefined;
    #contentResult ="";
    get ContentResult(){return this.contentResult;}
    set ContentResult(_contentResult){this.contentResult = _contentResult;}
    constructor() { }
    async Post(_url,_body={}, formdata=false){
        this.Url = _url;
        this.Body = _body;
        let result = new HttpClientResponse();
        try {
            if(formdata){ this.ContentResult = await $.ajax({ url : this.Url, data:this.Body,cache:false,contentType:false,processData:false, type : 'POST'}); }else{ this.ContentResult = await $.ajax({ url : this.Url, data:this.Body, type : 'POST' }); }
            //console.log(this.contentResult);
            if(this.ContentResult != null && this.ContentResult != 'null'){
                if(this.ContentResult.indexOf('<!DOCTYPE html>') == -1){
                    if(this.ContentResult.indexOf("document.location.href=") == -1){
                        let obj = JSON.parse(this.ContentResult);
                        if (obj.result == undefined){
                            result = new HttpClientResponse(true,obj.data,obj.message,SsyConvert.ObjectLength(obj.data),'Proceso exitoso',obj.state);
                        }else{
                            if(this.ContentResult.indexOf("CD1000") != -1){
                                result = new HttpClientResponse(false,null,'Estimado(a), Opción inválida, no se pudo encontrar una opción valida para el modelo. Gracias.',0,'Opción inválida');
                            } else {
                                if(SsyConvert.ToString(obj,'title',null) == null && SsyConvert.ToString(obj,'title',null) ==''){
                                    obj.title = 'Error desconocido';
                                }  
                                result = new HttpClientResponse(obj.result,obj.data,obj.message,SsyConvert.ObjectLength(obj.data),obj.title,obj.state);
                            }  
                        }
                    } else { result = new HttpClientResponse(false,null,'Estimado(a), Ruta de API no encontrada o nombre de controlador no existe. verifique por favor. Gracias','Ruta no encontrada');}
                } else { result = new HttpClientResponse(false,null,'Estimado(a), Ruta de API no encontrada o nombre de controlador no existe. verifique por favor. Gracias','Ruta no encontrada');}
                
            } else { result = new HttpClientResponse(false,null,'Valor devuelto de response es "NULL", verifique bien y vuelva a intentarlo','Error de sistema');}
        } catch(ex){ result = new HttpClientResponse(false,null,ex.message !=   undefined ? ex.message + ' | ' + this.ContentResult : ex + ' | ' + this.ContentResult);}
        return result;
    }
}
class SsyConvert {
    static ToObject(_object = null,_indice = null,_default_value = null){
        let result = _default_value;
        try {
            if(_object != null){
                if(_indice != null){
                    if(_object[_indice] != undefined){ result = _object[_indice]; }
                } else {
                    if(_object != undefined){ result = _object; }
                }
            }
        } catch(ex){}
        return result;
    }
    static ToString(_object = null,_indice = null,_default_value = ""){
        let result = _default_value;
        try {
            if(_object != null){
                if(_indice != null){
                    if(_object[_indice] != undefined){ result = _object[_indice]; }
                } else {
                    if(_object != undefined){ result = _object+""; }
                }
            }
        } catch(ex){}
        return result;
    }
    static ToInt(_object = null,_indice = null,_default_value = 0){
        let result = _default_value;
        try {
            if(_object != null){
                if(_indice != null){
                    if(_object[_indice] != undefined){ result = parseInt(_object[_indice]); }
                } else {
                    if(_object != undefined){ result = parseInt(_object+""); }
                }
            }
        } catch(ex){}
        return result;
    }
    static ToFloat(_object = null,_indice = null,_default_value = 0.0){
        let result = _default_value;
        try {
            if(_object != null){
                if(_indice != null){
                    if(_object[_indice] != undefined){ result = parseFloat(_object[_indice]); }
                } else {
                    if(_object != undefined){ result = parseFloat(_object+""); }
                }
            }
        } catch(ex){}
        return result;
    }

    static ObjectLength(object){
        let r = 0;
        try { if(object != null){ if(object.length != undefined){ r = object.length;}  }
        } catch(ex){ }
        return r;
    }
}

$(document).ready(function(){
    
});