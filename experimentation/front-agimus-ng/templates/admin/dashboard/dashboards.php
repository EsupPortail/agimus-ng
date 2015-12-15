<?php $title="Liste des tableaux de bord" ?>
<?php ob_start () ?>
<li ><a href="<?php echo $root_uri; ?>/admin/index.php">Accueil <span class="sr-only">Accueil</span></a></li>
<li ><a href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li class="active"><a href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
<li ><a href="<?php echo $root_uri; ?>/admin/index.php/graphes">Graphiques</a></li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
          <div class="table-responsive">
            <a href="<?php echo $root_uri; ?>/admin/index.php/dashboards?action=new" class="btn btn-default">Ajouter un nouveau tableau de bord</a>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Titre</th>
                  <th>Description</th>
                  <th>roles</th>
                  <th>Url</th>
                  <th>Graphiques</th>
                  <th>Action</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($dashboards as $dashboard): ?>
                <tr>
                  <td><?php echo $dashboard->getId(); ?></td>
                  <td><?php echo $dashboard->getTitle(); ?></td>
                  <td><?php echo $dashboard->getDescription(); ?></td>
                  <td><?php foreach($dashboard->getRoles() as $role){ echo "$role "; } ?></td>
                  <td><?php echo substr($dashboard->getUrl(), 0, 100); ?>...</td>
                  <td><?php foreach($dashboard->getGraphes() as $graphe){ echo "- ".$graphe->getTitle()."<br/>"; } ?></td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/dashboards">
                      <input type="hidden" name="action" value="modify">
                      <input type="hidden" name="id" value="<?php echo $dashboard->getId(); ?>">
                      <input type="submit" value="modifier" class="btn btn-default">
                    </form>
                  </td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/dashboards">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $dashboard->getId(); ?>">
                      <input type="submit" value="supprimer" class="btn btn-default">
                    </form>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
          </div>
<?php $content=ob_get_clean()?>

<?php include __DIR__ . '/../layout.php' ?>