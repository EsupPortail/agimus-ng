<?php $title="Liste des tableaux de bord" ?>
<?php ob_start () ?>
<li ><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php">Aide <span class="sr-only">Accueil</span></a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li><a class="btn btn-info btn-md active" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/exports">Exports</a></li>
 <li>&nbsp;</li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
          <div class="table-responsive">
            <a href="<?php echo $root_uri; ?>/admin/index.php/dashboards?action=new" class="btn btn-info btn-md">Ajouter un nouveau tableau de bord</a>
            <table  class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Titre</th>
                  <th>Description</th>
                  <th>roles</th>
                  <th>Url</th>
                  <th>Action</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody id="sortable">
                <?php foreach($dashboards as $dashboard): ?>
                  
                <tr class="ui-state-default" id="item_<?php echo $dashboard->getId(); ?>">
                  <td><?php echo $dashboard->getId(); ?></td>
                  <td><?php echo $dashboard->getTitle(); ?></td>
                  <td><?php echo $dashboard->getDescription(); ?></td>
                  <td><?php foreach($dashboard->getRoles() as $role){ echo "$role "; } ?></td>
                  <td><?php echo substr($dashboard->getUrl(), 0, 100); ?>...</td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/dashboards">
                      <input type="hidden" name="action" value="modify">
                      <input type="hidden" name="id" value="<?php echo $dashboard->getId(); ?>">
                      <input type="submit" value="modifier" class="btn btn-info">
                    </form>
                  </td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/dashboards">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $dashboard->getId(); ?>">
                      <input type="submit" value="supprimer" class="btn btn-danger">
                    </form>
                </tr>
              
              <?php endforeach ?>
              </tbody>
              
            </table>
            
 
            
          </div>
 
            <script>
                 $( function() {
                    $( "#sortable" ).sortable({
                         axis: 'y',
                         update: function (event, ui) {
                             var data = $(this).sortable('serialize');

                             // POST to server using $.post or $.ajax
                             $.ajax({
                                 data: data,
                                 type: 'POST',
                                 url: '/admin/index.php/dash_reorder'
                             });
                         }
                    });
                    $( "#sortable" ).disableSelection();
                 } );
             </script>
    
<?php $content=ob_get_clean()?>

<?php include __DIR__ . '/../layout.php' ?>