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

     <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://code.jquery.com/jquery-1.12.4.js"></script>
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>
  
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
      
        <div class="col-sm-3 col-md-2 sidebar">
           <div class="sidebar-brand">
            <img alt="Logo"  style="margin:0 auto;background-color:#333" src="<?php echo $root_uri; ?>/assets/images/logo.png">
            </div>
          <ul class="nav nav-sidebar">
            <?php echo $menu ?>
            <div style="text-align: center">
            <a href="<?php echo $root_uri; ?>/index.php"  class="btn btn-info btn-s active" role="button"  >
                   <img alt="Logout" src="<?php echo $root_uri; ?>/assets/images/undo-arrow.png" style="width:24px">
            </a>
            </div>
          </ul>
        </div>
        <div class="col-sm-9 col-sm-offset-3 col-md-10 col-md-offset-2 main">
          <?php if(isset($msg)) { ?>
            <div class="alert alert-<?php echo $msg['level']; ?> fade in">
            <a href="#" class="close" data-dismiss="alert">&times;</a>
            <strong> <?php echo $msg['message']; ?> </strong> 
            </div>
          <?php } ?>
          <h1 class="page-header">Administration</h1>
          <h2 class="sub-header"><?php echo $title ?></h2>
          <?php echo $content ?>
        </div>
      </div>
    </div>

    
  


    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo $root_uri; ?>/assets/js/bootstrap.min.js"></script>
  
    
    
  </body>
</html>


