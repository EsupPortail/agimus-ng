<?php

    function redirect_to_default_dashboard_action($user) {
        global $index_path, $user, $dashboards, $root_uri;
        header( "refresh:0;url=".$root_uri."/index.php/dashboard/".$dashboards[0]->getId()."/");
        //require $index_path.'/templates/admin/dashboard/dashboards.php';
    }

    function show_dashboard_action($id) {
        global $index_path, $user, $dashboards, $root_uri;

        if(isset($id) && $id!=-1) {
            $dashboard = get_dashboard_by_id($id);
        }
        $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("d")-1,   date("Y"))) ;
        $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-1,   date("Y"))) ;
        require $index_path.'/templates/dashboard.php';
        
        
    }

    function show_graphe_action($id) {
        global $index_path, $user, $dashboards, $root_uri;
        $graphe = get_graphe_by_id($id);
        $show_graphe=false;
        if(is_object($graphe)) {
            $allowed_graphes = get_graphe_from_dashboards($dashboards);
            if(in_array ( $graphe , $allowed_graphes)) {
                $show_graphe=true;
                $startDate = isset($_GET['startDate']) ? $_GET['startDate'] : date("Y-m-d", mktime(0, 0, 0, date("m")-1, date("d")-1,   date("Y"))) ;
                $endDate = isset($_GET['endDate']) ? $_GET['endDate'] : date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")-1,   date("Y"))) ;
            } else {
                $msg = array("level"=>"danger", "message"=>"Vous n'etes pas autorisé à visualiser ce graphique");
            }
        } else {
            $msg = array("level"=>"danger", "message"=>"Erreur lors de la recuperation du graphique ".$graphe);
        }
        require $index_path.'/templates/graphe.php';

    }

