<?php 
  if(!isset($form_dashboard) || (isset($form_dashboard) && is_object($form_dashboard))) {
    if(isset($form_dashboard)) $title="Modification du tableau de bord ".$form_dashboard->getTitle();
    else $title="Ajouter un nouveau tableau de bord" ;
  } else {
    $title="Erreur";
  }

  if(isset($msg) && $msg['level']=="success") {
    header( "refresh:2;url=$root_uri/admin/index.php/dashboards" );
  }
?>
<?php ob_start () ?>
<li ><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php">Aide <span class="sr-only">Accueil</span></a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li><a class="btn btn-info btn-md active" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
 <li>&nbsp;</li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
  
  <?php if(isset($msg) && $msg['level']=="success") { ?>
  <p> Vous allez être redirigé dans 3 secondes.</p>
  <p> Sinon cliquez : <a href="<?php echo $root_uri; ?>/admin/index.php/dashboards" class="btn btn-default">Retour</a> </p>
  <?php } else { ?>

          <div class="table-responsive">
            <?php if(!isset($form_dashboard) || (isset($form_dashboard) && is_object($form_dashboard))) { ?>
            <form id="form" name="form" method="post">

              <?php if(isset($form_dashboard) && $_GET['action']=="delete") { ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $form_dashboard->getId(); ?>">
                <p> Etes-vous sur de vouloir supprimer ce tableau de bord ? </p>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="oui" placeholder="Oui" name="oui"> Oui
                  </label>
                </div>
              <?php } else { ?>
                <?php if(isset($form_dashboard)) { ?>
                <input type="hidden" name="action" value="modify">
                <input type="hidden" name="id" value="<?php echo $form_dashboard->getId(); ?>">
                <?php } else { ?>
                <input type="hidden" name="action" value="new">
                <?php } ?>

                <div class="form-group">
                  <label for="title">Titre *</label>
                  <input required type="text" class="form-control" id="title" placeholder="Titre" name="title" <?php if(isset($form_dashboard)) echo "value='".$form_dashboard->getTitle()."'"; ?> >
                </div>
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" rows="3" id="description" placeholder="Description" name="description" ><?php if(isset($form_dashboard)) echo "".$form_dashboard->getDescription().""; ?></textarea>
                </div>
                <div class="form-group">
                  <label for="roles">Rôles *</label>
                   <select multiple class="form-control" id="roles" placeholder="role1,role2" name="roles[]" required>
                    <?php
                    $roles = array();
                    if(isset($form_dashboard)) $roles = $form_dashboard->getRoles();
                    foreach($user_roles as $user_role) {
                        $selected="";
                        if (in_array($user_role, $roles)) $selected="selected";
                        echo "<option value=\"$user_role\" $selected>$user_role</option>";
                    } 
                    ?>
                  </select>
                  <span id="rolesHelpBlock" class="help-block">Maintenez appuyé « Ctrl », ou « Commande (touche pomme) » sur un Mac, pour en sélectionner plusieurs.</span>
                </div>
                <div class="form-group">
                  <label for="url">Url</label>
                  <input type="url" class="form-control" id="url" placeholder="Url" name="url" <?php if(isset($form_dashboard)) echo "value=\"".$form_dashboard->getUrl()."\""; ?> >
                  <span id="urlHelpBlock" class="help-block">Dans Kibana, utilisez l'url issu du menu Share > Permalink > Saved Object.</span>
                </div>
                </div>
              <?php } ?>
              <button type="submit" class="btn btn-success">Envoyer</button>
              <button type="reset" class="btn btn-warning">Effacer</button>
              <a href="<?php echo $root_uri; ?>/admin/index.php/dashboards" class="btn btn-info">Retour</a>
            </form>
            <?php } ?>
          </div>
  <?php } ?>
<?php $content=ob_get_clean()?>

<?php include __DIR__ . '/../layout.php' ?>
