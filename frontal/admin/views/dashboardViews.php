<?php

    function list_all_dashboard_action() {
        global $index_path, $root_uri;
        $dashboards = get_all_dashboard();
        require $index_path.'/templates/admin/dashboard/dashboards.php';
    }

    function create_dashboard_action() {
        global $index_path, $user_roles, $root_uri;

        if(isset($_POST['action']) && $_POST['action']=="new") {
            $send_roles="";
            if(isset($_POST['roles']) && isset($_POST['title'])) {
                foreach($_POST['roles'] as $index => $role) {
                    if($index==0) $send_roles.= $role;
                    else $send_roles.= ",".$role;
                }
                $new_dashboard = create_new_dashboard($_POST['title'], $_POST['description'], $send_roles, $_POST['url']);
            } else {
                $msg = array("level"=>"danger", "message"=>"Vous devez renseigner au moins un role d'accès et un titre à ce tableau de bord");
            }
            if(is_object($new_dashboard)) $msg = array("level"=>"success", "message"=>"le tableau de bord a bien été créé");
            else $msg = array("level"=>"danger", "message"=>"Erreur lors de la creation du tableau de bord");
        }
        require $index_path.'/templates/admin/dashboard/form.php';
    }

    function update_dashboard_action($id) {
        global $index_path, $user_roles, $root_uri;
        $form_dashboard = get_dashboard_by_id($id);

        if(is_object($form_dashboard)) {

            if(isset($_POST['action']) && $_POST['action']=="modify" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
                $send_roles="";
                if(isset($_POST['roles']) && isset($_POST['title'])) {
                    foreach($_POST['roles'] as $index => $role) {
                        if($index==0) $send_roles.= $role;
                        else $send_roles.= ",".$role;
                    }
                    $form_dashboard = update_dashboard($_POST['id'], $_POST['title'], $_POST['description'], $send_roles, $_POST['url']);
                } else {
                    $msg = array("level"=>"danger", "message"=>"Vous devez renseigner au moins un role d'accès et un titre à ce tableau de bord");
                }
                if(is_object($form_dashboard)) $msg = array("level"=>"success", "message"=>"le tableau de bord a bien été modifié");
                else $msg = array("level"=>"danger", "message"=>"Erreur lors de la mise à jour du tableau de bord");
            }


        } else {
            $msg = array("level"=>"danger", "message"=>"Erreur lors de la recuperation du tableau de bord ".$form_dashboard);
        }
        require $index_path.'/templates/admin/dashboard/form.php';
    }





    function delete_dashboard_action($id) {
        global $index_path, $root_uri;

        $form_dashboard = get_dashboard_by_id($id);
        if(is_object($form_dashboard)) {

            if(isset($_POST['action']) && $_POST['action']=="delete" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
                if(isset($_POST['oui'])) {
                    $delete = delete_dashboard_by_id($id);
                    if($delete=="ok") $msg = array("level"=>"success", "message"=>"le tableau de bord a bien été supprimé");
                    else $msg = array("level"=>"danger", "message"=>"Erreur lors de la suppression : ".$delete);
                } else {
                    $msg = array("level"=>"warning", "message"=>"Vous devez cocher la case pour supprimer le tableau de bord");
                }
            }
        } else {
            $msg = array("level"=>"danger", "message"=>"Erreur lors de la recuperation du graphique ".$form_dashboard);
        }

        require $index_path.'/templates/admin/dashboard/form.php';
    }
