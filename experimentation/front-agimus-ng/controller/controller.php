<?php
// model.php
require $index_path.'/entity/User.php';
require $index_path.'/entity/Graphe.php';
require $index_path.'/entity/Dashboard.php';

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
        if($username==$default_admin_username) $user = create_new_user($username, $email, "ROLE_ADMIN");
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
        $result = mysqli_query($link,'SELECT * FROM `dashboard`');
        
    } else {
        $query = 'SELECT * FROM `dashboard` WHERE `roles` = ""';
        foreach ($user->getRoles() as $role) {
            if (trim($role)!=false) {
                $query = $query.' OR `roles` like "%'.$role.'%"';
            }
        }
        $result = mysqli_query($link,$query);
    }
    $dashboards = array();
    while($dashboard = mysqli_fetch_object($result, 'Dashboard')) {
        $dashboard = $dashboard->setGraphe(get_graphe_from_dashboard($dashboard));
        $dashboards[] = $dashboard;
    }
    close_database_connection($link);
    return $dashboards;
}

function get_graphe_from_dashboards($dashboards) {
    $list_dashboard_id = array();
    foreach($dashboards as $index=>$dash) {
        $list_dashboard_id[] = $dash->getId();
    }
    $link = open_database_connection();
    $sql = "SELECT DISTINCT `graphe`.`id`, `graphe`.`title`,`graphe`. `url`, `graphe`.`description` FROM `graphe`\n"
    . "JOIN `dashboard_graphe`\n"
    . "ON `graphe`.`id` = `dashboard_graphe`.`graphe_id`\n"
    . "JOIN `dashboard`\n"
    . "ON `dashboard_graphe`.`dashboard_id` = `dashboard`.`id`\n"
    . "WHERE `dashboard`.`id` in (".implode(",", $list_dashboard_id).")";
    if($result=mysqli_query($link,$sql)) {
        $graphes = array();
        while($graphe = mysqli_fetch_object($result, 'Graphe')) {
            $graphes[] = $graphe;
        }
    } else {
        return "<br />Error deleting record: " . mysqli_error();
    }
    close_database_connection($link);
    return $graphes;
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
        echo "<br />Error inserting record: " . mysqli_error();
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
        echo "<br />Error inserting record: " . mysqli_error();
    }
    close_database_connection($link);
    return $user;
}

/****** GRAPHE ****/

function get_graphe_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $query = 'SELECT `id`, `title`, `url`, `description` FROM graphe WHERE id = '.$id;
    if($result=mysqli_query($link,$query)) {
        $graphe = mysqli_fetch_object($result, 'Graphe');
        close_database_connection($link);
        return $graphe;
    } else {
        close_database_connection($link);
        return " " .mysqli_error();
    }
}

function get_all_graphe() {
    $link = open_database_connection();
    $query = 'SELECT `id`, `title`, `url`, `description` FROM graphe ';
    $result=mysqli_query($link,$query);
    $graphes = array();
    while($graphe = mysqli_fetch_object($result, 'Graphe')) {
        $graphes[] = $graphe;
    }
    close_database_connection($link);
    return $graphes;
}
function create_new_graphe($title, $url, $description="") {
    $link = open_database_connection();
    $graphe=null;
    $title = addslashes(stripslashes(trim(htmlspecialchars($title))));
    $url = addslashes(stripslashes(trim(htmlspecialchars($url))));
    $description = addslashes(stripslashes(trim(htmlspecialchars($description))));
    $sql_insert = 'INSERT INTO `graphe` (`id`, `title`, `url`, `description`) VALUES (NULL, \''.$title.'\', \''.$url.'\', \''.$description.'\');';
    if(mysqli_query($link,$sql_insert)) {
        $graphe = get_graphe_by_id(mysqli_insert_id($link));
    } else {
        echo "<br />Error inserting record: " . mysqli_error();
    }
    close_database_connection($link);
    return $graphe;
}

function update_graphe($id, $title, $url, $description="") {
    $link = open_database_connection();
    $id = intval($id);
    $graphe=null;
    $title = addslashes(stripslashes(trim(htmlspecialchars($title))));
    $url = addslashes(stripslashes(trim(htmlspecialchars($url))));
    $description = addslashes(stripslashes(trim(htmlspecialchars($description))));
    $sql_update = 'UPDATE `graphe` SET `title` = \''.$title.'\', `url` = \''.$url.'\', `description` = \''.$description.'\'  WHERE `graphe`.`id` ='.$id.';';
    if(mysqli_query($link,$sql_update)) {
        $graphe = get_graphe_by_id($id);
    } else {
        echo "<br />Error updating record: " . mysqli_error();
    }
    close_database_connection($link);
    return $graphe;
}

function delete_graphe_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $delete_query = 'DELETE FROM `dashboard_graphe` WHERE `graphe_id` = '.$id;
    if(mysqli_query($link,$delete_query)) {
        $query = 'DELETE FROM `graphe` WHERE `id` = '.$id;
        if(mysqli_query($link,$query)) {
            close_database_connection($link);
            return "ok";
        } else {
            return "<br />Error deleting record: " . mysqli_error();
        }
    } else {
        close_database_connection($link);
        return "<br />Error deleting record: " . mysqli_error();
    }
}

/***** DASHBOARD ***/

function get_dashboard_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $query = 'SELECT `id`, `title`, `description`, `roles`, `url` FROM dashboard WHERE id = '.$id;
    if($result=mysqli_query($link,$query)) {
        $dashboard = mysqli_fetch_object($result, 'Dashboard');
        close_database_connection($link);
        $dashboard = $dashboard->setGraphe(get_graphe_from_dashboard($dashboard));
        return $dashboard;
    } else {
        close_database_connection($link);
        return " " .mysqli_error();
    }
}

function get_all_dashboard() {
    $link = open_database_connection();
    $query = 'SELECT `id`, `title`, `description`, `roles`, `url` FROM dashboard';
    $result=mysqli_query($link,$query);
    $dashboards = array();
    while($dashboard = mysqli_fetch_object($result, 'Dashboard')) {
        $dashboard = $dashboard->setGraphe(get_graphe_from_dashboard($dashboard));
        $dashboards[] = $dashboard;
    }
    close_database_connection($link);
    return $dashboards;
}

function get_graphe_from_dashboard($dashboard) {
    $link = open_database_connection();
    $sql = "SELECT `graphe`.`id`, `graphe`.`title`,`graphe`. `url`, `graphe`.`description` FROM `graphe`\n"
    . "JOIN `dashboard_graphe`\n"
    . "ON `graphe`.`id` = `dashboard_graphe`.`graphe_id`\n"
    . "JOIN `dashboard`\n"
    . "ON `dashboard_graphe`.`dashboard_id` = `dashboard`.`id`\n"
    . "WHERE `dashboard`.`id`= ".$dashboard->getId();
    $result=mysqli_query($link,$sql);
    $graphes = array();
    while($graphe = mysqli_fetch_object($result, 'Graphe')) {
        $graphes[] = $graphe;
    }
    close_database_connection($link);
    return $graphes;
}

function create_new_dashboard($title, $description="", $roles, $graphes_id=array(), $url="" ) {
    $link = open_database_connection();
    $dashboard=null;
    $title = addslashes(stripslashes(trim(htmlspecialchars($title))));
    $description = addslashes(stripslashes(trim(htmlspecialchars($description))));
    $roles = addslashes(stripslashes(trim(htmlspecialchars($roles))));
    $url = addslashes(stripslashes(trim(htmlspecialchars($url))));
    $sql_insert = 'INSERT INTO `dashboard` (`id`, `title`, `description`, `roles`, `url`) VALUES (NULL, \''.$title.'\', \''.$description.'\', \''.$roles.'\', \''.$url.'\');';
    if(mysqli_query($link,$sql_insert)) {
        if(isset($graphes_id) && !empty($graphes_id)) {
            $list_values="";
            $dashboard_id = mysqli_insert_id($link);
            foreach($graphes_id as $index => $graphe_id) {
                if($index==0) $list_values.= '(\''.$dashboard_id.'\', \''.$graphe_id.'\')';
                else $list_values.= ', '.'(\''.$dashboard_id.'\', \''.$graphe_id.'\')';
            }
            $sql_insert_dashboard_graphique = 'INSERT INTO `dashboard_graphe` (`dashboard_id`, `graphe_id`) VALUES '.$list_values.';';
            if(mysqli_query($link,$sql_insert_dashboard_graphique)) {
                $dashboard = get_dashboard_by_id($dashboard_id);
            } else {
                $dashboard = "<br />Error inserting record: " . mysqli_error();
            }
        } else {
            $dashboard = get_dashboard_by_id(mysqli_insert_id($link));
        }
    } else {
        $dashboard = "<br />Error inserting record: " . mysqli_error();
    }
    close_database_connection($link);
    return $dashboard;
}

function update_dashboard($id, $title, $description="", $roles, $graphes_id=array(), $url="" ) {
    $link = open_database_connection();
    $dashboard=null;
    $id = intval($id);

    $title = addslashes(stripslashes(trim(htmlspecialchars($title))));
    $description = addslashes(stripslashes(trim(htmlspecialchars($description))));
    $roles = addslashes(stripslashes(trim(htmlspecialchars($roles))));
    $url = addslashes(stripslashes(trim(htmlspecialchars($url))));
    $sql_update = 'UPDATE `dashboard` SET `title` = \''.$title.'\', `roles` = \''.$roles.'\',`url` = \''.$url.'\', `description` = \''.$description.'\'   WHERE `id` = '.$id.';';
    

    if(mysqli_query($link,$sql_update)) {
        //delete all previous relation
        $delete_query = 'DELETE FROM `dashboard_graphe` WHERE `dashboard_id` = '.$id;
        if(mysqli_query($link,$delete_query)) {
            if(isset($graphes_id) && !empty($graphes_id)) {
                $list_values="";
                foreach($graphes_id as $index => $graphe_id) {
                    if($index==0) $list_values.= '(\''.$id.'\', \''.$graphe_id.'\')';
                    else $list_values.= ', '.'(\''.$id.'\', \''.$graphe_id.'\')';
                }
                $sql_insert_dashboard_graphique = 'INSERT INTO `dashboard_graphe` (`dashboard_id`, `graphe_id`) VALUES '.$list_values.';';
                if(mysqli_query($link,$sql_insert_dashboard_graphique)) {
                    $dashboard = get_dashboard_by_id($id);
                } else {
                    $dashboard = "<br />Error inserting record: " . mysqli_error();
                }
            } else {
                $dashboard = get_dashboard_by_id($id);
            }
        } else {
                $dashboard = "<br />Error deleting grpahe dashboard relations record: " . mysqli_error();
        }   
    } else {
        echo "<br />Error updating record: " . mysqli_error();
        $dashboard = "<br />Error updating record: " . mysqli_error();
    }
    close_database_connection($link);
    return $dashboard;
}

function delete_dashboard_by_id($id) {
    $link = open_database_connection();
    $id = intval($id);
    $delete_query = 'DELETE FROM `dashboard_graphe` WHERE `dashboard_id` = '.$id;
    if(mysqli_query($link,$delete_query)) {
        $query = 'DELETE FROM `dashboard` WHERE `id` = '.$id;
        if(mysqli_query($link,$query)) {
            close_database_connection($link);
            return "ok";
        } else {
            return "<br />Error deleting record: " . mysqli_error();
        }
    } else {
        close_database_connection($link);
        return "<br />Error deleting record: " . mysqli_error();
    }
}
