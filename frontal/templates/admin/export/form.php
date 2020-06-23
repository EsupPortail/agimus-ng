<?php 
  if(isset($form_export)) $title="Export ID : ".$form_export->getId();
  
  if(isset($msg) && $msg['level']=="success") {
    header( "refresh:3;url=$root_uri/admin/index.php/exports" );
  }
?>
<?php ob_start () ?>
<li ><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php">Aide <span class="sr-only">Accueil</span></a></li>
<li><a class="btn btn-info btn-md active" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/exports">Utilisateurs</a></li>
<li><a class="btn btn-info btn-md " style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
<li><a class="btn btn-info btn-md active" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/exports">Exports</a></li> 
<li>&nbsp;</li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
  
  <?php if(isset($msg) && $msg['level']=="success") { ?>
  <p> Vous allez être redirigé dans 3 secondes.</p>
  <p> Sinon cliquez : <a href="<?php echo $root_uri; ?>/admin/index.php/exports" class="btn btn-default">Retour</a> </p>
  <?php } else { ?>

          <div class="table-responsive" style="overflow:hidden">
            <form id="form" name="form" method="post">

              <?php if(isset($form_export) && $_GET['action']=="delete") { ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?php echo $form_export->getId(); ?>">
                <p> Etes-vous sur de vouloir supprimer cet export ? </p>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="oui" placeholder="Oui" name="oui"> Oui
                  </label>
                </div>
                <button type="submit" class="btn btn-default">Envoyer</button>
              <?php } elseif(isset($form_export) && $_GET['action']=="reactive") { ?>
                <input type="hidden" name="action" value="reactive">
                <input type="hidden" name="id" value="<?php echo $form_export->getId(); ?>">
                <p> Etes-vous sur de vouloir reactiver cet export ? </p>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="oui" placeholder="Oui" name="oui"> Oui
                  </label>
                </div>
                <button type="submit" class="btn btn-default">Envoyer</button>
              <?php } elseif(isset($form_export) && $_GET['action']=="desactive") { ?>
                <input type="hidden" name="action" value="desactive">
                <input type="hidden" name="id" value="<?php echo $form_export->getId(); ?>">
                <p> Etes-vous sur de vouloir désactiver cet export ? </p>
                <div class="checkbox">
                  <label>
                    <input type="checkbox" id="oui" placeholder="Oui" name="oui"> Oui
                  </label>
                </div>
                <button type="submit" class="btn btn-default">Envoyer</button>
              <?php } else { ?>
                    <div class="form-group row">
                      <label for="inputEmail3" class="col-sm-2 col-form-label">Date de demande</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo $form_export->getDate_demand(); ?>" >
                      </div>
                    </div>
                    <div class="form-group row">
                      <label  class="col-sm-2 col-form-label">Date de traitement</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo $form_export->getDate_execute(); ?>" >
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">ID du dashboard</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo $form_export->getDashboard_id(); ?>" >
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">Email</label>
                      <div class="col-sm-8">
                        <input type="text" class="form-control" value="<?php echo $form_export->getEmail(); ?>" >
                      </div>
                    </div>
                    <div class="form-group row">
                      <label class="col-sm-2 col-form-label">URL Kibana</label>
                      <div class="col-sm-8">
                          <textarea  class="form-control" ><?php echo $form_export->getUrl(); ?></textarea>
                      </div>
                    </div>
                  
              <?php } ?>
              
              <a href="<?php echo $root_uri; ?>/admin/index.php/exports" class="btn btn-info">Retour</a>
            </form>
          </div>
  <?php } ?>
<?php $content=ob_get_clean()?>

<?php include __DIR__ . '/../layout.php' ?>