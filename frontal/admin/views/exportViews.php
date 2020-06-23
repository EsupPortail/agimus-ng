<?php

    function list_all_export_action() {
        global $index_path, $root_uri, $export_actif;
        $exports = get_all_export();
        require $index_path.'/templates/admin/export/exports.php';
    }

    function view_export_action($id) {
        global $index_path,  $root_uri;

        $form_export = get_export_by_id($id);

        require $index_path.'/templates/admin/export/form.php';
    }

    function delete_export_action($id) {
        global $index_path, $root_uri;

        $form_export = get_export_by_id($id);

        if(isset($_POST['action']) && $_POST['action']=="delete" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
            if(isset($_POST['oui'])) {
                $delete = delete_export_by_id($id);
                if($delete=="ok") $msg = array("level"=>"success", "message"=>"l'utilisateur a bien été supprimé");
                else $msg = array("level"=>"danger", "message"=>"Erreur lors de la suppression : ".$delete);
            } else {
                $msg = array("level"=>"warning", "message"=>"Vous devez cocher la case pour supprimer l'utilisateur");
            }
        }

        require $index_path.'/templates/admin/export/form.php';
    }

    function reactive_export_action($id) {
        global $index_path, $root_uri;

        $form_export = get_export_by_id($id);

        if(isset($_POST['action']) && $_POST['action']=="reactive" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
            if(isset($_POST['oui'])) {
                $delete = reactive_export($id);
                if($delete=="ok") $msg = array("level"=>"success", "message"=>"Export réactivé");
                else $msg = array("level"=>"danger", "message"=>"Erreur lors de la réactivation : ".$delete);
            } else {
                $msg = array("level"=>"warning", "message"=>"Vous devez cocher la case pour réactiver");
            }

        }

        require $index_path.'/templates/admin/export/form.php';
    }

    function desactive_export_action($id) {
        global $index_path, $root_uri;

        $form_export = get_export_by_id($id);

        if(isset($_POST['action']) && $_POST['action']=="desactive" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
            if(isset($_POST['oui'])) {
                $delete = desactive_export($id);
                if($delete=="ok") $msg = array("level"=>"success", "message"=>"Export désactivé");
                else $msg = array("level"=>"danger", "message"=>"Erreur lors de la désactivation : ".$delete);
            } else {
                $msg = array("level"=>"warning", "message"=>"Vous devez cocher la case pour désactiver");
            }

        }

        require $index_path.'/templates/admin/export/form.php';
    }
