<?php
// model.php
require $index_path.'/entity/User.php';
require $index_path.'/entity/Dashboard.php';
require $index_path.'/entity/Export.php';

function open_database_connection() {
    global $index_path;
    require $index_path.'/config/config.php';
    $link = mysqli_connect($db_host,$db_user,$db_password);
    mysqli_select_db($link,$db_name);
    return $link;
}

function close_database_connection($link) {
    mysqli_close ($link);
}

function open_ldap_connection() {
    global $index_path;
    require $index_path.'/config/config.php';

	$ldapLink = ldap_connect($LDAP['HOST']);
	if (!$ldapLink) die("Erreur : l'application est indisponible (erreur connexion annuaire).");
	@ldap_start_tls ( $ldapLink );
	if (!ldap_bind($ldapLink, $LDAP['BIND_DN'], $LDAP['BIND_PASSWORD'])) die("Erreur : l'application est indisponible (erreur authentification annuaire).");

	return $ldapLink;

}

function close_ldap_connection($ldapLink) {
    @ldap_close($ldapLink);
}

/****** USER ****/

function get_or_create_user($username, $email="") {
    global $default_admin_username;
    global $index_path;
    require $index_path.'/config/config.php';

	$link = open_database_connection();
    $query = 'SELECT `id`, `username`, `password`, `email`, `roles` FROM user WHERE username = "'.$username.'"';
    $result=mysqli_query($link,$query);
    if (mysqli_num_rows($result)==0) {
        //create user
        if($username==$default_admin_username) $user = create_new_user($username, $email, "ADMIN");
        else {
			// connect to ldap to get metadata en roles
			$ldapLink = open_ldap_connection();
			$search_result = @ldap_search($ldapLink, $LDAP['PEOPLE_BASE_DN'], "(uid=".$username.")", $LDAP['PEOPLE_ATTRS']);
			if (!$search_result) die("Erreur : l'application est indisponible (erreur récuperation d'information d'annuaire).");

			$entries = ldap_get_entries($ldapLink, $search_result);

			if($entries['count'] > 1) die("Erreur : l'application est indisponible (erreur récuperation d'information d'annuaire plus d'une personne).");

			// get mail from ldap
			$email = $entries[0]['mail'][0];

			// get roles from ldap (grouper attributs)
			$ldap_roles= array();
			for($i=0; $i < $entries[0][$LDAP['GROUP_ATTRS']]['count']; $i++) {
				foreach ($user_roles_mapping as $roleKey => $role_value){
					if($entries[0][$LDAP['GROUP_ATTRS']][$i] == $role_value) {
						$ldap_roles[] = $roleKey;
					}
				}
			}
			close_ldap_connection($ldapLink);
			$user = create_new_user($username, $email);
			if(!empty($ldap_roles)){
				$user->setRoles(join($ldap_roles, ','));
			}
		}
    } else {
		$ldapLink = open_ldap_connection();
		$search_result = @ldap_search($ldapLink, $LDAP['PEOPLE_BASE_DN'], "(uid=".$username.")", $LDAP['PEOPLE_ATTRS']);
		if (!$search_result) die("Erreur : l'application est indisponible (erreur récuperation d'information d'annuaire).");
		$entries = ldap_get_entries($ldapLink, $search_result);

		if($entries['count'] > 1) die("Erreur : l'application est indisponible (erreur récuperation d'information d'annuaire plus d'une personne).");

		// get mail from ldap
		$email = $entries[0]['mail'][0];

		// get roles from ldap (grouper attributs)
		$ldap_roles= array();
		for($i=0; $i < $entries[0][$LDAP['GROUP_ATTRS']]['count']; $i++) {
			foreach ($user_roles_mapping as $roleKey => $role_value){
				if($entries[0][$LDAP['GROUP_ATTRS']][$i] == $role_value) {
					$ldap_roles[] = $roleKey;
				}
			}
		}
		close_ldap_connection($ldapLink);

                $user = mysqli_fetch_object($result, 'User');
		$currentRoles = $user->getRoles();
		$full_roles = array_unique(array_merge($currentRoles, $ldap_roles));
		$user->setRoles(join($full_roles, ','));
    }
    close_database_connection($link);

    return $user;
}

function get_user_dashboards($user) {
    $link = open_database_connection();
    if($user->isAdmin()){
        $result = mysqli_query($link,'SELECT * FROM `dashboard` ORDER BY `order`');

    } else {
        $query = 'SELECT * FROM `dashboard` WHERE `roles` = ""';
        foreach ($user->getRoles() as $role) {
            if (trim($role)!=false) {
                $query = $query.' OR `roles` like "%'.$role.'%" ';
            }
        }
        $query .= ' ORDER BY `order`';
        $result = mysqli_query($link,$query);
    }
    $dashboards = array();
    while($dashboard = mysqli_fetch_object($result, 'Dashboard')) {
        $dashboards[] = $dashboard;
    }
    close_database_connection($link);
    return $dashboards;
}

function get_all_user() {
    $link = open_database_connection();
    $query = 'SELECT `id`, `username`, `password`, `email`, `roles` FROM user ';
    $result=mysqli_query($link,$query);
    $users = array();
    while($user = mysqli_fetch_object($result, 'User')) {
        $users[] = $user;
    }
    close_database_connection($link);
    return $users;
}

function get_user_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $query = 'SELECT `id`, `username`, `password`, `email`, `roles` FROM user WHERE id = '.$id;
    $result=mysqli_query($link,$query);
    $user = mysqli_fetch_object($result, 'User');
    close_database_connection($link);
    return $user;
}

function delete_user_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $query = 'DELETE FROM `user` WHERE `user`.`id` = '.$id;
    if(mysqli_query($link,$query)) {
        close_database_connection($link);
        return "ok";
    } else {
        close_database_connection($link);
        return "<br />Error deleting record: " . mysqli_error();
    }
}

function create_new_user($username, $email=null, $roles="") {
    $link = open_database_connection();
    $user=null;
    $username = addslashes(stripslashes(trim(htmlspecialchars($username))));
    $roles = addslashes(stripslashes(trim(htmlspecialchars(str_replace(" ", "", $roles)))));

    if($email==null) {
        $sql_insert = 'INSERT INTO `user` (`id`, `username`, `password`, `email`, `roles`) VALUES (NULL, \''.$username.'\', NULL, NULL, \''.$roles.'\');';
    } else {
        $email = addslashes(stripslashes(trim(htmlspecialchars($email))));
        $sql_insert = 'INSERT INTO `user` (`id`, `username`, `password`, `email`, `roles`) VALUES (NULL, \''.$username.'\', NULL, \''.$email.'\', \''.$roles.'\');';
    }
    if(mysqli_query($link,$sql_insert)) {
        $query = 'SELECT `id`, `username`, `password`, `email`, `roles` FROM user WHERE id = "'.mysqli_insert_id($link).'"';
        $result=mysqli_query($link,$query);
        $user = mysqli_fetch_object($result, 'User');
    } else {
        echo "<br />Error inserting record 1: " . $sql_insert . mysqli_error();
    }
    close_database_connection($link);
    return $user;
}

function update_user($id, $username, $email=null, $roles="") {
    $link = open_database_connection();
    $id = intval($id);
    $user=null;
    $username = addslashes(stripslashes(trim(htmlspecialchars($username))));
    $roles = addslashes(stripslashes(trim(htmlspecialchars(str_replace(" ", "", $roles)))));

    if($email==null)
        $sql_update = 'UPDATE `user` SET `username` = \''.$username.'\', `email` = NULL, `roles` = \''.$roles.'\'  WHERE `user`.`id` ='.$id.';';
    else {
        $email = addslashes(stripslashes(trim(htmlspecialchars($email))));
        $sql_update = 'UPDATE `user` SET `username` = \''.$username.'\', `email` = \''.$email.'\', `roles` = \''.$roles.'\'  WHERE `user`.`id` ='.$id.';';
    }
    if(mysqli_query($link,$sql_update)) {
        $query = 'SELECT `id`, `username`, `password`, `email`, `roles` FROM user WHERE id = "'.$id.'"';
        $result=mysqli_query($link,$query);
        $user = mysqli_fetch_object($result, 'User');
    } else {
        echo "<br />Error inserting record 2: " . $query . mysqli_error();
    }
    close_database_connection($link);
    return $user;
}

/***** DASHBOARD ***/

function get_dashboard_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $query = 'SELECT `id`, `title`, `description`, `roles`, `url` FROM dashboard WHERE id = '.$id;
    if($result=mysqli_query($link,$query)) {
        $dashboard = mysqli_fetch_object($result, 'Dashboard');
        close_database_connection($link);
        return $dashboard;
    } else {
        close_database_connection($link);
        return " " .mysqli_error();
    }
}

function get_all_dashboard() {
    $link = open_database_connection();
    $query = 'SELECT `id`, `title`, `description`, `roles`, `url` FROM dashboard ORDER BY `order`';
    $result=mysqli_query($link,$query);
    $dashboards = array();
    while($dashboard = mysqli_fetch_object($result, 'Dashboard')) {
        $dashboards[] = $dashboard;
    }
    close_database_connection($link);
    return $dashboards;
}

function create_new_dashboard($title, $description="", $roles, $url="" ) {
    $link = open_database_connection();
    $dashboard=null;
    $title = addslashes(stripslashes(trim(htmlspecialchars($title))));
    $description = addslashes(stripslashes(trim(htmlspecialchars($description))));
    $roles = addslashes(stripslashes(trim(htmlspecialchars($roles))));
    $url = addslashes(stripslashes(trim(htmlspecialchars($url))));
    $sql_insert = 'INSERT INTO `dashboard` (`id`, `title`, `description`, `roles`, `url`) VALUES (NULL, \''.$title.'\', \''.$description.'\', \''.$roles.'\', \''.$url.'\');';
    if(mysqli_query($link,$sql_insert)) {
        $dashboard = get_dashboard_by_id(mysqli_insert_id($link));
    } else {
        $dashboard = "<br />Error inserting dashboard : " . $sql_insert . mysqli_error();
    }
    close_database_connection($link);
    return $dashboard;
}

function update_dashboard($id, $title, $description="", $roles, $url="" ) {
    $link = open_database_connection();
    $dashboard=null;
    $id = intval($id);

    $title = addslashes(stripslashes(trim(htmlspecialchars($title))));
    $description = addslashes(stripslashes(trim(htmlspecialchars($description))));
    $roles = addslashes(stripslashes(trim(htmlspecialchars($roles))));
    $url = addslashes(stripslashes(trim(htmlspecialchars($url))));
    $sql_update = 'UPDATE `dashboard` SET `title` = \''.$title.'\', `roles` = \''.$roles.'\',`url` = \''.$url.'\', `description` = \''.$description.'\'   WHERE `id` = '.$id.';';


    if(mysqli_query($link,$sql_update)) {
        $dashboard = get_dashboard_by_id($id);
    } else {
        echo "<br />Error updating record: " . mysqli_error();
        $dashboard = "<br />Error updating record: " . mysqli_error();
    }
    close_database_connection($link);
    return $dashboard;
}

function update_dashreorder() {

    $items = $_POST['item'];

    $ret[] = "";

    if( !empty( $items ) ) {

        $link = open_database_connection();

        foreach( $items as $rang=>$ordering ){

            $ret[$rang] = $ordering;

            if (is_numeric($ordering) ) {

                $sql_update = 'UPDATE `dashboard` SET `order` = \''.$rang.'\'   WHERE `id` = '.$ordering.';';

                if( ! mysqli_query($link,$sql_update)) {
                    $ret[] = "<br />Error updating items: " . mysqli_error();
                }
            }

        }


        close_database_connection($link);
    }else{
        $ret[] = 'Invalid items';

    }

    return print_r($ret);
}


function delete_dashboard_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $query = 'DELETE FROM `dashboard` WHERE `id` = '.$id;
    if(mysqli_query($link,$query)) {
        return "ok";
    } else {
        return "<br />Error deleting record: " . mysqli_error();
    }
    close_database_connection($link);
}


/*** EXPORT ***/

function export_register($user) {

    $start  = $_POST['startDate'];
    $end    = $_POST['endDate'];
    $dash   = $_POST['dashboard'];
    $urlloc = $_POST['url'];
    $mail   = $user->getEmail();
    $result = '';

    //$oDash = get_dashboard_by_id($dash);
    $url   =  addslashes( base64_decode($urlloc) );

    /* Champs BDD
     * ID
     *
     *  `start_date` varchar(10) NOT NULL,
        `end_date` varchar(10) NOT NULL,
        `dashboard_id` int(11) NOT NULL,
     *  `url`
        `email` varchar(60) COLLATE utf8_unicode_ci NOT NULL,
        `format` varchar(10),
        `type` varchar(10),
        `date_demand` timestamp NOT NULL,
        `date_execute` timestamp ,
     *
     * $dashboard->getCheckedUrl($startDate, $endDate);
     *
     */

    $link = open_database_connection();

    $sql_insert = "INSERT INTO `export` ( `start_date`,`end_date`,`dashboard_id`,`url`,`email`,`format`,`type`,`date_demand`,`date_execute`) "
                                . " VALUES ( '$start', '$end', '$dash','$url','$mail', 'jpeg', 'L', NOW(), '0000-00-00 00:00:00' );";

    if( mysqli_query($link,$sql_insert) ) {
        header('Status: 201 Created');
    }else{
        $dashboard = "<br />Error inserting : " . $sql_insert . mysqli_error();
        header('Status: 500 Internal Server Error');
        echo $dashboard;
    }

    close_database_connection($link);


}


function get_all_export() {
    $link = open_database_connection();
    $query = 'SELECT * FROM export ORDER BY `date_demand` DESC';
    $result=mysqli_query($link,$query);
    $exports = array();
    while($export = mysqli_fetch_object($result, 'Export')) {
        $exports[] = $export;
    }
    close_database_connection($link);
    return $exports;
}

function get_export_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $query = 'SELECT * FROM export WHERE id = '.$id;
    $result=mysqli_query($link,$query);
    $export = mysqli_fetch_object($result, 'Export');
    close_database_connection($link);
    return $export;
}

function delete_export_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $query = 'DELETE FROM `export` WHERE `export`.`id` = '.$id;
    if(mysqli_query($link,$query)) {
        close_database_connection($link);
        return "ok";
    } else {
        close_database_connection($link);
        return "<br />Error deleting record: " . mysqli_error();
    }
}

function reactive_export($id) {

    $link = open_database_connection();

    $sql = "UPDATE export SET date_execute='0000-00-00 00:00:00' WHERE id='$id'";


    if( ! mysqli_query($link,$sql) ) {
        $ret = "<br />Error updating : " . $sql_insert . mysqli_error();
    }else{
        $ret = "ok";
    }

    close_database_connection($link);

    return $ret;

}

function desactive_export($id) {

    $link = open_database_connection();

    $sql = "UPDATE export SET date_execute=NOW() WHERE id='$id'";

    if( ! mysqli_query($link,$sql) ) {
        $ret = "<br />Error updating : " . $sql_insert . mysqli_error();
    }else{
        $ret = "ok";
    }

    close_database_connection($link);

    return $ret;

}
/* API EXPORT */
function get_list_export() {

    $link = open_database_connection();

    $sql = "SELECT EX.*, DS.title FROM export EX"
            . " LEFT JOIN dashboard DS"
            . " ON EX.dashboard_id = DS.id"
            . " WHERE date_execute='0000-00-00 00:00:00'";

    $result=mysqli_query($link,$sql);

    if( ! $result ) {
        $line[] = "Request error : " . $sql . mysqli_error();
    }else {
        while ($row = $result->fetch_row()) {
            $line[]= join(";", $row);
        }
    }

    // Concataine et ajoute linefeed au dernier, sinon pas vu par bash
    echo join( PHP_EOL, $line).PHP_EOL;

    close_database_connection($link);

}

function close_export_by_id($id) {

    $link = open_database_connection();

    $sql = "UPDATE export SET date_execute=NOW() WHERE id='$id'";

    if( mysqli_query($link,$sql) ) {
        header('Status: 201 Created');
    }else{
        $dashboard = "<br />Error updating : " . $sql_insert . mysqli_error();
        header('Status: 500 Internal Server Error');
        echo $dashboard;
    }

    close_database_connection($link);

}
