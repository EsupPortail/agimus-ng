<?php $title="Liste des utilisateurs" ?>
<?php ob_start () ?>
<li ><a class="btn btn-info btn-md " style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php">Aide <span class="sr-only">Accueil</span></a></li>
<li><a class="btn btn-info btn-md active" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/exports">Exports</a></li>
 <li>&nbsp;</li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
          <div class="table-responsive">
            <a href="?action=new" class="btn btn-info">Ajouter un nouvel utilisateur</a>
            <table class="table table-striped">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Login</th>
                  <th>Courriel</th>
                  <th>Rôles</th>
                  <th>Action</th>
                  <th>Action</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach($users as $user): ?>
                <tr>
                  <td><?php echo $user->getId(); ?></td>
                  <td><?php echo $user->getUsername(); ?></td>
                  <td><?php echo $user->getEmail(); ?></td>
                  <td><?php foreach($user->getRoles() as $role){ echo "$role "; } ?></td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/users">
                      <input type="hidden" name="action" value="modify">
                      <input type="hidden" name="id" value="<?php echo $user->getId(); ?>">
                      <input type="submit" value="modifier" class="btn btn-info">
                    </form>
                  </td>
                  <td>
                    <form method="get" action="<?php echo $root_uri; ?>/admin/index.php/users">
                      <input type="hidden" name="action" value="delete">
                      <input type="hidden" name="id" value="<?php echo $user->getId(); ?>">
                      <input type="submit" value="supprimer" class="btn btn-danger">
                    </form>
                </tr>
              <?php endforeach ?>
              </tbody>
            </table>
          </div>
<?php $content=ob_get_clean()?>

<?php include __DIR__ . '/../layout.php' ?>