<?php 
  if(isset($form_user)) $title="Modification de l'utilisateur ".$form_user->getId();
  else $title="Ajouter un nouvel utilisateur" ;

  if(isset($msg) && $msg['level']=="success") {
    header( "refresh:3;url=$root_uri/admin/index.php/users" );
  }
?>
<?php ob_start () ?>
<li ><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php">Aide <span class="sr-only">Accueil</span></a></li>
<li><a class="btn btn-info btn-md active" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li><a class="btn btn-info btn-md " style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
 <li>&nbsp;</li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
  
  <?php if(isset($msg) && $msg['level']=="success") { ?>
  <p> Vous allez être redirigé dans 3 secondes.</p>
  <p> Sinon cliquez : <a href="<?php echo $root_uri; ?>/admin/index.php/users" class="btn btn-default">Retour</a> </p>
  <?php } else { ?>

          <div class="table-responsive">
            <form id="form" name="form" method="post">

              <?php if(isset($form_user) && $_GET['action']=="delete") { ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $form_user->getId(); ?>">
                <p> Etes-vous sur de vouloir supprimer cet utilisateur ? </p>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="oui" placeholder="Oui" name="oui"> Oui
                  </label>
                </div>
              <?php } else { ?>
                <?php if(isset($form_user)) { ?>
                <input type="hidden" name="action" value="modify">
                <input type="hidden" name="id" value="<?php echo $form_user->getId(); ?>">
                <?php } else { ?>
                <input type="hidden" name="action" value="new">
                <?php } ?>
                <div class="form-group">
                  <label for="username">Login *</label>
                  <input required type="text" class="form-control" id="username" placeholder="Username" name="username" <?php if(isset($form_user)) echo "value='".$form_user->getUsername()."'"; ?> >
                </div>
                <div class="form-group">
                  <label for="courriel">Courriel</label>
                  <input type="email" class="form-control" id="courriel" placeholder="Courriel" name="email" <?php if(isset($form_user)) echo "value='".$form_user->getEmail()."'"; ?> >
                </div>
                <div class="form-group">
                  <label for="roles">Rôles</label>
                   <select multiple class="form-control" id="roles" placeholder="role1,role2" name="roles[]" >
                    <?php
                    $roles = array();
                    if(isset($form_user)) $roles = $form_user->getRoles();
                    foreach($user_roles as $user_role) {
                        $selected="";
                        if (in_array($user_role, $roles)) $selected="selected";
                        echo "<option value=\"$user_role\" $selected>$user_role</option>";
                    } 
                    ?>
                  </select>
                  <span id="roleshelpBlock" class="help-block">Maintenez appuyé « Ctrl », ou « Commande (touche pomme) » sur un Mac, pour en sélectionner plusieurs.</span>
                </div>
              <?php } ?>
              <button type="submit" class="btn btn-success">Envoyer</button>
              <button type="reset" class="btn btn-warning">Effacer</button>
              <a href="<?php echo $root_uri; ?>/admin/index.php/users" class="btn btn-info">Retour</a>
            </form>
          </div>
  <?php } ?>
<?php $content=ob_get_clean()?>

<?php include __DIR__ . '/../layout.php' ?>