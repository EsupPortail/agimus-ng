<?php

    function list_all_user_action() {
        global $index_path, $root_uri;
        $users = get_all_user();
        require $index_path.'/templates/admin/user/users.php';
    }

    function create_user_action() {
        global $index_path, $user_roles, $root_uri;
        if(isset($_POST['action']) && $_POST['action']=="new") {
            $send_roles="";
            if(isset($_POST['roles'])) {
                foreach($_POST['roles'] as $index => $role) {
                    if($index==0) $send_roles.= $role;
                    else $send_roles.= ",".$role;
                }
            }
            if(empty($_POST['email'])) {
                $new_user = create_new_user($_POST['username'], null, $send_roles);
            } else {
                $new_user = create_new_user($_POST['username'], $_POST['email'], $send_roles);
            }
            if(isset($new_user)) $msg = array("level"=>"success", "message"=>"l'utilisateur a bien été créé");
            else $msg = array("level"=>"danger", "message"=>"Erreur lors de la creation");
        }
        require $index_path.'/templates/admin/user/form.php';
    }

    function update_user_action($id) {
        global $index_path, $user_roles, $root_uri;
        $form_user = get_user_by_id($id);
        if(isset($_POST['action']) && $_POST['action']=="modify" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
            if(isset($_POST['username'])) {
                $send_roles="";
                if(isset($_POST['roles'])) {
                    foreach($_POST['roles'] as $index => $role) {
                        if($index==0) $send_roles.= $role;
                        else $send_roles.= ",".$role;
                    }
                }
                $form_user = update_user($_POST['id'], $_POST['username'], $_POST['email'], $send_roles);
                if(isset($form_user)) $msg = array("level"=>"success", "message"=>"l'utilisateur a bien été modifié");
                else $msg = array("level"=>"danger", "message"=>"Erreur lors de la mise à jour de l'utilisateur");
            } else {
                $msg = array("level"=>"danger", "message"=>"Le champ login est obligatoire");
            }
        }
        require $index_path.'/templates/admin/user/form.php';
    }

    function delete_user_action($id) {
        global $index_path, $user_roles, $root_uri;

        $form_user = get_user_by_id($id);

        if(isset($_POST['action']) && $_POST['action']=="delete" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
            if(isset($_POST['oui'])) {
                $delete = delete_user_by_id($id);
                if($delete=="ok") $msg = array("level"=>"success", "message"=>"l'utilisateur a bien été supprimé");
                else $msg = array("level"=>"danger", "message"=>"Erreur lors de la suppression : ".$delete);
            } else {
                $msg = array("level"=>"warning", "message"=>"Vous devez cocher la case pour supprimer l'utilisateur");
            }
        }

        require $index_path.'/templates/admin/user/form.php';
    }

