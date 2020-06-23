<?php

    function redirect_to_default_dashboard_action($user) {
        global $index_path, $dashboards, $root_uri;
        header( "refresh:0;url=".$root_uri."/index.php/dashboard/".$dashboards[0]->getId()."/");
    }

    function show_dashboard_action($id) {
        global $index_path, $user, $dashboards, $root_uri, $mail_support, $export_actif;

        if(isset($id) && $id!=-1) {
            $dashboard = get_dashboard_by_id($id);
        }
        $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("d")-1,   date("Y"))) ;
        $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+1,   date("Y"))) ;
        $large = isset($_GET['large']) ? 'toggled' : '' ;
        require $index_path.'/templates/dashboard.php';


    }
