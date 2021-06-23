<?php
    session_start();
    require_once 'Config/Config.php';
    require_once 'Framework/SsyFramework.php';
    
    //autoregistro de clases 
    spl_autoload_register(function($class){
        if(file_exists(DirApp.DirFramework.$class.php)){ include DirFramework.$class.php; }
        if(file_exists(DirApp.DirModels.$class.php)){ require_once DirApp.DirModels.$class.php; }
    });
?>