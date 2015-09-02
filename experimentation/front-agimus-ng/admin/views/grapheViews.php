<?php

    function list_all_graphe_action() {
        global $index_path, $root_uri;
        $graphes = get_all_graphe();
        require $index_path.'/templates/admin/graphe/graphes.php';
    }
    
    function create_graphe_action() {
        global $index_path, $root_uri;

        if(isset($_POST['action']) && $_POST['action']=="new") {
            $new_graphe = create_new_graphe($_POST['title'], $_POST['url'], $_POST['description']);

            if(isset($new_graphe)) $msg = array("level"=>"success", "message"=>"le graphique a bien été créé");
            else $msg = array("level"=>"danger", "message"=>"Erreur lors de la creation du graphique");
        }


        require $index_path.'/templates/admin/graphe/form.php';
    }
    
    function update_graphe_action($id) {
        global $index_path, $root_uri;

        $form_graphe = get_graphe_by_id($id);
        if(is_object($form_graphe)) {
            if(isset($_POST['action']) && $_POST['action']=="modify" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
                if(isset($_POST['title']) && isset($_POST['url'])) {
                    $form_graphe = update_graphe($_POST['id'], $_POST['title'], $_POST['url'], $_POST['description']);
                    if(isset($form_graphe)) $msg = array("level"=>"success", "message"=>"le graphique a bien été modifié");
                    else $msg = array("level"=>"danger", "message"=>"Erreur lors de la mise à jour du graphique");
                } else {
                    $msg = array("level"=>"warning", "message"=>"Les champs titre et url sont obligatoires");
                }
            }
        } else {
            $msg = array("level"=>"danger", "message"=>"Erreur lors de la recuperation du graphique ".$form_graphe);
        }
        require $index_path.'/templates/admin/graphe/form.php';
    }
    
    function delete_graphe_action($id) {
        global $index_path, $root_uri;

        $form_graphe = get_graphe_by_id($id);
        if(is_object($form_graphe)) {

            if(isset($_POST['action']) && $_POST['action']=="delete" && isset($_POST['id']) && $_POST['id']==$_GET['id']) {
                if(isset($_POST['oui'])) {
                    $delete = delete_graphe_by_id($id);
                    if($delete=="ok") $msg = array("level"=>"success", "message"=>"le graphique a bien été supprimé");
                    else $msg = array("level"=>"danger", "message"=>"Erreur lors de la suppression : ".$delete);
                } else {
                    $msg = array("level"=>"warning", "message"=>"Vous devez cocher la case pour supprimer le graphique");
                }
            }
        } else {
            $msg = array("level"=>"danger", "message"=>"Erreur lors de la recuperation du graphique ".$form_graphe);
        }

        require $index_path.'/templates/admin/graphe/form.php';
    }
    

