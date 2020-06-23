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

    <!-- HTML5 shim and Respond.js for IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
      <script src="https://oss.maxcdn.com/html5shiv/3.7.2/html5shiv.min.js"></script>
      <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->
  </head>
  <body>
    <div id="wrapper"  class="<?php  echo $large  ?>" >

        <!-- Sidebar -->
        <div id="sidebar-wrapper">
            <ul class="sidebar-nav">
                <li class="sidebar-brand">
                    <a href="/">
                        <img alt="Logo" src="<?php echo $root_uri; ?>/assets/images/logo.png">
                    </a>
                </li>

                <li>&nbsp;</li>
                <?php foreach($dashboards as $dash): ?>
                <li <?php if($dash->getId()==$dashboard->getId()) echo 'class="active"'; ?> >
                    <a class="btn btn-info btn-md active" style="color:#333" role="button"  href="<?php echo $root_uri; ?>/index.php/dashboard/<?php echo $dash->getId(); ?>/?startDate=<?php echo $startDate; ?>&endDate=<?php echo $endDate; ?>"><?php echo $dash->getTitle(); ?></a>
                </li>
                <?php endforeach ?>

                <li class="sidebar-brand">
                </li >
                <div style="text-align: center">
                    <a href="<?php echo $root_uri; ?>/index.php?logout="  class="btn btn-info btn-s active" role="button"  >
                        <img alt="Logout" src="<?php echo $root_uri; ?>/assets/images/off-on.png" style="width:24px">
                    </a>

                    <?php if($user->isAdmin()) { ?>
                    <a href="<?php echo $root_uri; ?>/admin/index.php" class="btn btn-info btn-s active" role="button" >
                        <img alt="Logout" src="<?php echo $root_uri; ?>/assets/images/settings.png" style="width:24px">
                    </a>
                    <?php } ?>
                </div>
            </ul>
        </div>
        <!-- /#sidebar-wrapper -->

        <!-- Page Content -->
        <div id="page-content-wrapper">

            <div class="container-fluid">
              <!-- Page Header -->
              <div id="headtitle" class="row">
                  <div class="col-lg-12">
                      <a href="#menu-toggle" class="btn btn-default" style="margin-left:-20px;padding: 0" id="menu-toggle">
                        <img alt="Logout" src="<?php echo $root_uri; ?>/assets/images/hamburger-menu.png" style="width:34px">
                      </a>

                      <a href="#frame-toggle" class="btn btn-default" id="frame-toggle">
                          <img alt="Logout" src="<?php echo $root_uri; ?>/assets/images/full-screen.png" style="width:20px">
                      </a>

                      <div id="reportrange" class="btn btn-default" >
                          <i class="glyphicon glyphicon-calendar fa fa-calendar"></i>
                          <span><?php echo $startDate." - ".$endDate; ?></span> <b class="caret"></b>
                      </div>

                      <?php if(isset($dashboard)) { ?>

                      <span class="btn btn-info disabled" >
                          <?php echo $dashboard->getTitle() ?>
                      </span>

                      <?php if(isset($export_actif) && $export_actif) { ?>
                      <span class="btn btn-info" id="btn_export">
                          Export
                      </span>

                      <?php }  ?>

                      <?php } else { ?>
                        <h1 class="page-header">Agimus NG
                          <small>Vous n'avez accès à aucun tableau de bord.</small>
                        </h1>
                        <p>&nbsp;Aucun tableau de bord disponible.</p>
                      <?php } ?>
                      <span style="font-size:1.5em;margin:0 20px;float: right; font-style: italic">
                          Agimus NG <?php if (isset($mail_support)) {echo '- <a href="'.$mail_support.'">Support</a>'; } ?>
                      </span>
                  </div>
              </div>
              <!-- /.row -->
              <!-- Projects Row -->
              <?php if(isset($dashboard)) { ?>
              <?php if($dashboard->getUrl()!="") { ?>
                    <iframe id="dashboard-frame" allowfullscreen src="<?php echo $dashboard->getCheckedUrl($startDate, $endDate); ?>"  style="display:block;width:100%;"/> </iframe>
              <?php } else { ?>
              <div class="danger">
              <?php echo "<p class='text-danger'>Dashboard incomplet : aucune url définie</p>"; ?>
              </div>
              <?php } ?>
              <?php } ?>
              <hr>


            <!-- Footer -->
            <footer>
                <div class="row text-center">
                    <div class="col-lg-12">

                    </div>
                </div>
                <!-- /.row -->
            </footer>
            </div>
            <!-- /.container -->
        </div>
        <!-- /#page-content-wrapper -->

    </div>
    <!-- /#wrapper -->


    <!-- jQuery (necessary for Bootstrap's JavaScript plugins) -->
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.11.3/jquery.min.js"></script>
    <!-- Include all compiled plugins (below), or include individual files as needed -->
    <script src="<?php echo $root_uri; ?>/assets/js/bootstrap.min.js"></script>

    <script type="text/javascript" src="<?php echo $root_uri; ?>/assets/bootstrap-daterangepicker-master/moment.js"></script>
    <script type="text/javascript" src="<?php echo $root_uri; ?>/assets/bootstrap-daterangepicker-master/daterangepicker.js"></script>

    <script>

        $( document ).ready(function() {
            var wHeight = $(window).height();
            $("#dashboard-frame").css("height",(wHeight-100)+"px");
        });

        $( window ).resize(function() {
            var wHeight = $(window).height();
            $("#dashboard-frame").css("height",(wHeight-100)+"px");
        });

        $("#menu-toggle").click(function(e) {
            e.preventDefault();
            $("#wrapper").toggleClass("toggled");
            $(".page-header").toggle();
            $("hr").toggle();

        });

	$("#frame-toggle").click(function(e) {
		e.preventDefault();
		var element = document.getElementById("dashboard-frame");

		if (element.requestFullScreen) {
			element.requestFullScreen();
		} else if (element.mozRequestFullScreen) {
			element.mozRequestFullScreen();
		} else if (element.webkitRequestFullScreen) {
			element.webkitRequestFullScreen();
		} else if (element.msRequestFullscreen) {
			element.msRequestFullscreen();
		}
		else {
			alert("désolé");
		}
	});

        $("#btn_export").click(function(e) {

                var urlloc = window.btoa( $("#dashboard-frame")[0].contentWindow.location );

                $.ajax({
                   data: 'startDate=<?php echo $startDate ?>&endDate=<?php echo $endDate; ?>&dashboard=<?php echo $dashboard->getId(); ?>&url='+urlloc,
                   type: 'POST',
                   url: '/index.php/dash_export'
                });

                alert("Vous allez recevoir la copie d'écran par mail !");
        });


//	var fullScreen = false;
//	$("#framesize-toggle").click(function(e) {
//		e.preventDefault();
//
//
//		$("#wrapper").toggleClass("toggled");
//		$(".page-header").toggle();
//		$("#frame-toggle").toggle();
//		$("#menu-toggle").toggle();
//		$("hr").toggle();
//
//		var wHeight = $(window).height();
//
//		var frameCurrentHeight = $("#dashboard-frame").css("height");
//		frameCurrentHeight = frameCurrentHeight.replace('px', '');
//
//		console.log("TESTJM : "+frameCurrentHeight+" -- "+(wHeight-70))
//		if(fullScreen) {
//			$("#dashboard-frame").css("height","100%");
//			fullScreen = false;
//		}
//		else {
//			$("#dashboard-frame").css("height",(wHeight-70)+"px");
//			fullScreen = true;
//		}
//
//
//	});

    </script>

    <script type="text/javascript">
    $(function() {
    $('#reportrange').daterangepicker({
        format: 'YYYY-MM-DD',
        startDate: '<?php echo $startDate; ?>',
        endDate: '<?php echo $endDate; ?>',
        minDate: '2015-02-01',
        maxDate: '<?php echo date("Y-m-d", mktime(0, 0, 0, date("m"), date("d")+1,   date("Y"))) ; ?>',
        //dateLimit: { days: 60 },
        showDropdowns: true,
        showWeekNumbers: true,
        timePicker: false,
        timePickerIncrement: 1,
        timePicker12Hour: true,
        ranges: {
           'Aujourd\'hui': [moment(), moment().add(1, 'days')],
           'Hier': [moment().subtract(1, 'days'), moment()],
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
      if ($("#wrapper").hasClass("toggled")) {
        document.location.href=location.pathname+'?startDate='+startDate+'&endDate='+endDate+'&large';
      }else{
        document.location.href=location.pathname+'?startDate='+startDate+'&endDate='+endDate;
      }

    });

});

var i = 0;
$('iframe').each(function(){
  console.log($(this));
  var oDoc = this.contentWindow || this.contentDocument;
  /*if (oDoc.document) {
      oDoc = oDoc.document;
  }*/
  console.log(oDoc.location);
  console.log(oDoc.document.body.innerHTML);
  //if()
    //<title>Kibana-acces-denied</title>

});

</script>

  </body>
</html>
