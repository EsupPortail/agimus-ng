<?php 
  if(!isset($form_graphe) || (isset($form_graphe) && is_object($form_graphe))) {
    if(isset($form_graphe)) $title="Modification du graphique ".$form_graphe->getTitle();
    else $title="Ajouter un nouveau graphique" ;
  } else {
    $title="Erreur";
  }

  if(isset($msg) && $msg['level']=="success") {
    header( "refresh:2;url=$root_uri/admin/index.php/graphes" );
  }
?>
<?php ob_start () ?>
<li ><a href="<?php echo $root_uri; ?>/admin/index.php">Accueil <span class="sr-only">Accueil</span></a></li>
<li ><a href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li><a href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
<li class="active"><a href="<?php echo $root_uri; ?>/admin/index.php/graphes">Graphiques</a></li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
  
  <?php if(isset($msg) && $msg['level']=="success") { ?>
  <p> Vous allez être redirigé dans 3 secondes.</p>
  <p> Sinon cliquez : <a href="<?php echo $root_uri; ?>/admin/index.php/graphes" class="btn btn-default">Retour</a> </p>
  <?php } else { ?>

          <div class="table-responsive">
            <?php if(!isset($form_graphe) || (isset($form_graphe) && is_object($form_graphe))) { ?>
            <form id="form" name="form" method="post">

              <?php if(isset($form_graphe) && $_GET['action']=="delete") { ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $form_graphe->getId(); ?>">
                <p> Etes-vous sur de vouloir supprimer ce graphique ? </p>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="oui" placeholder="Oui" name="oui"> Oui
                  </label>
                </div>
              <?php } else { ?>
                <?php if(isset($form_graphe)) { ?>
                <input type="hidden" name="action" value="modify">
                <input type="hidden" name="id" value="<?php echo $form_graphe->getId(); ?>">
                <?php } else { ?>
                <input type="hidden" name="action" value="new">
                <?php } ?>

                <div class="form-group">
                  <label for="title">Titre *</label>
                  <input required type="text" class="form-control" id="title" placeholder="Titre" name="title" <?php if(isset($form_graphe)) echo "value=\"".$form_graphe->getTitle()."\""; ?> >
                </div>
                <div class="form-group">
                  <label for="url">Url *</label>
                  <input required type="url" class="form-control" id="url" placeholder="Url" name="url" <?php if(isset($form_graphe)) echo "value=\"".$form_graphe->getUrl()."\""; ?> >
                </div>
                <div class="form-group">
                  <label for="description">Description</label>
                  <textarea class="form-control" rows="3" id="description" placeholder="Description" name="description" ><?php if(isset($form_graphe)) echo "".$form_graphe->getDescription().""; ?></textarea>
                </div>
              <?php } ?>
              <button type="submit" class="btn btn-default">Envoyer</button>
              <button type="reset" class="btn btn-default">Effacer</button>
              <a href="<?php echo $root_uri; ?>/admin/index.php/graphes" class="btn btn-default">Retour</a>
            </form>
            <?php } ?>
          </div>
  <?php } ?>
<?php $content=ob_get_clean()?>

<?php include __DIR__ . '/../layout.php' ?>