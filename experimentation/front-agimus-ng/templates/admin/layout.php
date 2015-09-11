<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>Agimus - tableau de bord</title>

    <!-- Bootstrap -->
    <link href="<?php echo $root_uri; ?>/assets/css/bootstrap.min.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
    <link href="<?php echo $root_uri; ?>/assets/css/dashboard.css" rel="stylesheet">

  </head>
  <body>



    <div class="container-fluid">
      <div class="row">
        <?php if(isset($msg)) { ?>
        <div class="alert alert-<?php echo $msg['level']; ?> fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong> -> </strong> <?php echo $msg['message']; ?>
        </div>
        <?php } ?>
        <div class="col-sm-3 col-md-2 sidebar">
          <ul class="nav nav-sidebar">
            <?php echo $menu ?>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <h1 class="page-header">Administration</h1>
          <h2 class="sub-header"><?php echo $title ?></h2>
          <?php echo $content ?>
        </div>
      </div>
    </div>

    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo $root_uri; ?>/assets/js/bootstrap.min.js"></script>
  </body>
</html>


