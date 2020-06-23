<?php $title="Liste des Exports" ?>
<?php ob_start () ?>
<li ><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php">Aide <span class="sr-only">Accueil</span></a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li><a class="btn btn-info btn-md " style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
<li><a class="btn btn-info btn-md active" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/exports">Exports</a></li>
 <li>&nbsp;</li>
<?php $menu=ob_get_clean()?>

<?php
ob_start () ;
    if(isset($export_actif) && $export_actif) {
      ?>
          <div class="table-responsive">

            <table  class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Date demande</th>
                  <th>Date execution</th>
                  <th>Dashboard</th>
                  <th>Date debut</th>
                  <th>Date fin</th>
                  <th>Demandeur</th>
                  <th>Actions</th>

                </tr>
              </thead>
              <tbody id="sortable">
                <?php foreach($exports as $export): ?>

                <tr class="ui-state-default" id="item_<?php echo $export->getId(); ?>">
                  <td><?php echo $export->getId(); ?></td>
                  <td><?php echo $export->getDate_demand(); ?></td>
                  <td><?php echo $export->getDate_execute(); ?></td>
                  <td><?php echo $export->getDashboard_id(); ?></td>
                  <td><?php echo $export->getStart_date(); ?></td>
                  <td><?php echo $export->getEnd_date(); ?></td>
                  <td><?php echo $export->getEmail(); ?></td>


                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/exports">
                      <input type="hidden" name="action" value="view">
                      <input type="hidden" name="id" value="<?php echo $export->getId(); ?>">
                      <input type="submit" value="view" class="btn btn-success">
                    </form>
                  </td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/exports">
                      <input type="hidden" name="action" value="reactive">
                      <input type="hidden" name="id" value="<?php echo $export->getId(); ?>">
                      <input type="submit" value="réactiver" class="btn btn-warning">
                    </form>
                  </td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/exports">
                      <input type="hidden" name="action" value="desactive">
                      <input type="hidden" name="id" value="<?php echo $export->getId(); ?>">
                      <input type="submit" value="désactiver" class="btn btn-primary">
                    </form>
                  </td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/exports">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $export->getId(); ?>">
                      <input type="submit" value="supprimer" class="btn btn-danger">
                    </form>
                </tr>


              <?php endforeach ?>
              </tbody>

            </table>



          </div>


<?php
} else {
  echo "<div class='row'><p>Pour accéder à ce menu, activer l'export dans le fichier de configuration</p></div>";
}
$content=ob_get_clean();
?>

<?php include __DIR__ . '/../layout.php' ?>
