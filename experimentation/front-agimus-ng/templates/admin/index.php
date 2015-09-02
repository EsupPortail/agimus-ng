<?php $title="Bienvenue sur l'administration d'Agimus" ?>
<?php ob_start () ?>
<li class="active"><a href="<?php echo $root_uri; ?>/admin/index.php">Accueil <span class="sr-only">Accueil</span></a></li>
<li><a href="<?php echo $root_uri; ?>/admin/index.php/users">Utilisateurs</a></li>
<li><a href="<?php echo $root_uri; ?>/admin/index.php/dashboards">Tableaux de bord</a></li>
<li><a href="<?php echo $root_uri; ?>/admin/index.php/graphes">Graphiques</a></li>
<?php $menu=ob_get_clean()?>

<?php ob_start () ?>
<p> Utilisez le menu de gauche pour accéder aux différentes rubriques de l'administration </p>
<?php $content=ob_get_clean()?>

<?php include 'layout.php' ?>


