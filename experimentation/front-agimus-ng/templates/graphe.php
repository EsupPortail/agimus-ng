<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="Agimus-ng DashBoards">
    <meta name="author" content="Nicolas CAN">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <title>AgimusNG - tableau de bord</title>

    <!-- Bootstrap -->
    <link href="<?php echo $root_uri; ?>/assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?php echo $root_uri; ?>/assets/css/style.css" rel="stylesheet">
    <link rel="stylesheet" type="text/css" href="<?php echo $root_uri; ?>/assets/bootstrap-daterangepicker-master/daterangepicker-bs3.css" />
    <style>
    html,body{height:100%;margin:0;padding:0;}
    </style>

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body >
    
    <?php if(isset($msg)) { ?>
    <div class="alert alert-<?php echo $msg['level']; ?> fade in">
        <a href="#" class="close" data-dismiss="alert">&times;</a>
        <strong> -> </strong> <?php echo $msg['message']; ?>
    </div>
    <?php } ?>

    <?php if(isset($graphe) && is_object($graphe) && $show_graphe==true) { ?>
    <div class="row" style="">
      <div class="col-lg-12">
        <div class="pull-right">
            <a href="<?php echo $root_uri; ?>/index.php?logout=" class="btn btn-default" >Déconnexion</a>
        </div>
        <div id="reportrange" class="btn btn-default pull-right" >
            <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
            <span><?php echo $startDate." - ".$endDate; ?></span> <b class="caret"></b>
        </div>

        <?php if(isset($_SERVER['HTTP_REFERER'])) { ?>
          <a href="<?php echo $_SERVER['HTTP_REFERER']; ?>" class="btn btn-default" id="menu-toggle">retour</a>
        <?php } else { ?>
          <a href="<?php echo $root_uri; ?>/index.php" class="btn btn-default" id="menu-toggle">retour</a>
        <?php } ?>
      </div>
    </div>
    <iframe class="img-responsive" src="<?php echo $graphe->getCheckedUrl($startDate, $endDate); ?>"  style="display:block;width:99%;max-width:100%;height:95%;border:0"/> </iframe>
    <?php } ?>
  
    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo $root_uri; ?>/assets/js/bootstrap.min.js"></script>
        <script type="text/javascript" src="<?php echo $root_uri; ?>/assets/bootstrap-daterangepicker-master/moment.js"></script>
    <script type="text/javascript" src="<?php echo $root_uri; ?>/assets/bootstrap-daterangepicker-master/daterangepicker.js"></script>


    <!-- Menu Toggle Script -->
    <script type="text/javascript">
    $(function() { 
        $('#reportrange').daterangepicker({
            format: 'YYYY-MM-DD',
            startDate: '<?php echo $startDate; ?>',
            endDate: '<?php echo $endDate; ?>',
            minDate: '2015-02-01',
            maxDate: '<?php echo $endDate; ?>',
            dateLimit: { days: 60 },
            showDropdowns: true,
            showWeekNumbers: true,
            timePicker: false,
            timePickerIncrement: 1,
            timePicker12Hour: true,
            ranges: {
               'Aujourd\'hui': [moment(), moment()],
               'Hier': [moment().subtract(2, 'days'), moment().subtract(1, 'days')],
               '7 derniers jours': [moment().subtract(7, 'days'), moment()],
               '30 derniers jours': [moment().subtract(30, 'days'), moment()],
               'Mois en cours': [moment().startOf('month'), moment().endOf('month')],
               'Mois dernier': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
            },
            opens: 'left',
            drops: 'down',
            buttonClasses: ['btn', 'btn-sm'],
            applyClass: 'btn-primary',
            cancelClass: 'btn-default',
            separator: ' to ',
            locale: {
                applyLabel: 'Envoyer',
                cancelLabel: 'Annuler',
                fromLabel: 'De',
                toLabel: 'A',
                customRangeLabel: 'Custom',
                daysOfWeek: ['Di', 'Lu', 'Ma', 'Me', 'Je', 'Ve','Sa'],
                monthNames: ['Janvier', 'Février', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Aout', 'Septembre', 'Octobre', 'Novembre', 'Décembre'],
                firstDay: 1
            }
        }, function(start, end, label) {
            console.log(start.toISOString(), end.toISOString(), label);
            $('#reportrange span').html(start.format('MMMM D, YYYY') + ' - ' + end.format('MMMM D, YYYY'));
        });
        
        $('#reportrange').on('apply.daterangepicker', function(ev, picker) {
          var startDate = picker.startDate.format('YYYY-MM-DD');
          var endDate = picker.endDate.format('YYYY-MM-DD');
          document.location.href=location.pathname+'?startDate='+startDate+'&endDate='+endDate;
        });
     
    });
    </script>
  </body>
</html>


