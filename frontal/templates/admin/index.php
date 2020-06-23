<?php $title="Aide" ?>
<?php ob_start () ?>
<li ><a class="btn btn-info btn-md active" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php">Aide <span class="sr-only">Accueil</span></a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
<li><a class="btn btn-info btn-md" style="color:#333" role="button" href="<?php echo $root_uri; ?>/admin/index.php/exports">Exports</a></li>
 <li>&nbsp;</li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
<h3> Menu Utilisateurs</h3>
<p>
    Les utilisateurs liés au LDAP sont automatiquement affectés à des rôles prédéfinis dans le fichier de configuration de l'application<br>
    Ce sont ces rôles dans l'application qui ouvrent les accès.<br>
    Dans le formulaire de gestion d'un utilisateur, on ne voit pas ses rôles LDAP, mais uniquement les rôles définis localement.<br>
</p>
<h3> Menu tableaux de bord </h3>
<p>
    Les tableaux de bord, pointent sur les dashboard Kibana via une url.<br>
    Des rôles sont associés aux tableaux de bord donnant ainsi accès aux utilisateurs ayant ce rôle.
</p>

<?php $content=ob_get_clean()?>

<?php include 'layout.php' ?>


