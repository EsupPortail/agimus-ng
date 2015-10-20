<?php
    // AUTH
    $index_path = '..';
    // Load the settings from the central config file
    require_once $index_path.'/config/config.php';
    // Load the CAS lib
    require_once $phpcas_path . '/CAS.php';

    $pos_ind = strpos($_SERVER["REQUEST_URI"],'/admin/index.php');
    if ($pos_ind > 0) {
        $root_uri = substr($_SERVER["REQUEST_URI"],0,$pos_ind);
    } else {
        $root_uri = ""; //substr($_SERVER["REQUEST_URI"],0,-7);
    }

    phpCAS::setDebug();

    // Initialize phpCAS
    phpCAS::client(CAS_VERSION_2_0, $cas_host, $cas_port, $cas_context);

    // For production use set the CA certificate that is the issuer of the cert
    // on the CAS server and uncomment the line below
    // phpCAS::setCasServerCACert($cas_server_ca_cert_path);

    // For quick testing you can disable SSL validation of the CAS server.
    // THIS SETTING IS NOT RECOMMENDED FOR PRODUCTION.
    // VALIDATING THE CAS SERVER IS CRUCIAL TO THE SECURITY OF THE CAS PROTOCOL!
    phpCAS::setNoCasServerValidation();

    // force CAS authentication
    phpCAS::forceAuthentication();

    // at this step, the user has been authenticated by the CAS server
    // and the user's login name can be read with phpCAS::getUser().

    // logout if desired
    if (isset($_REQUEST['logout'])) {
      //phpCAS::logout();
      if ( isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on'){
            $url = "https://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $parseurl = parse_url($url);
            $dirpath = dirname($parseurl['path']);
            $service = "https://".$parseurl['host'].rtrim($dirpath, '/\\')."/";
        } else {
            $url = "http://".$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
            $parseurl = parse_url($url);
            $dirpath = dirname($parseurl['path']);
            $service = "http://".$parseurl['host'].rtrim($dirpath, '/\\')."/";
        }
      phpCAS::logoutWithRedirectService($service);
    }


    /*** CHECK IF USER IS KNOWN ***/

    // charge et initialise les bibliothèques globales
    
    require_once $index_path.'/controller/controller.php';

    $user = get_or_create_user(phpCAS::getUser());

    if($user->isAdmin()) {
      // route la requête en interne
      $uri=strtok($_SERVER["REQUEST_URI"],'?');
      if((''.$root_uri.'/admin/index.php'==$uri)||($root_uri.'/admin/'==$uri)){
        
        require $index_path.'/templates/admin/index.php';

      } elseif(''.$root_uri.'/admin/index.php/users'==$uri ) {
        require_once $index_path.'/admin/views/userViews.php';
        if(isset($_GET['action']) && $_GET['action']=="new") create_user_action();
        elseif(isset($_GET['action']) && $_GET['action']=="modify" && isset($_GET['id'])) update_user_action($_GET['id']);
        elseif(isset($_GET['action']) && $_GET['action']=="delete" && isset($_GET['id'])) delete_user_action($_GET['id']);
        else list_all_user_action();

      } elseif(''.$root_uri.'/admin/index.php/graphes'==$uri ) {
        require_once $index_path.'/admin/views/grapheViews.php';
        if(isset($_GET['action']) && $_GET['action']=="new") create_graphe_action();
        elseif(isset($_GET['action']) && $_GET['action']=="modify" && isset($_GET['id'])) update_graphe_action($_GET['id']);
        elseif(isset($_GET['action']) && $_GET['action']=="delete" && isset($_GET['id'])) delete_graphe_action($_GET['id']);
        else list_all_graphe_action();

      } elseif(''.$root_uri.'/admin/index.php/dashboards'==$uri ) {
        require_once $index_path.'/admin/views/dashboardViews.php';
        if(isset($_GET['action']) && $_GET['action']=="new") create_dashboard_action();
        elseif(isset($_GET['action']) && $_GET['action']=="modify" && isset($_GET['id'])) update_dashboard_action($_GET['id']);
        elseif(isset($_GET['action']) && $_GET['action']=="delete" && isset($_GET['id'])) delete_dashboard_action($_GET['id']);
        else list_all_dashboard_action();

      } else {
        header('Status: 404 Not Found');
        echo '<html><body><h1>Admin Page Not Found</h1></body></html>';
      }
    } else {
      header('HTTP/1.0 401 Unauthorized'); 
      echo '<html><body><h1>You are not authorized to access this page</h1></body></html>';
    }
?>



