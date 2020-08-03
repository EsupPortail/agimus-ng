<?php

    $db_host = 'localhost';
    $db_user = 'user';
    $db_password = 'password';
    $db_name = 'dbname';

    $phpcas_path = '/path/to/phpCAS/';

    # Attention à conserver le rôle ADMIN, il a une signification particulière dans le code
    $user_roles = array('ADMIN', 'MANAGER', 'USER');

    # Attention à conserver le rôle ADMIN, il a une signification particulière dans le code
    $user_roles_mapping = array(
      "ADMIN" => "nom_du_groupe_ldap_ADMIN",
      "MANAGER" => "nom_du_groupe_ldap_MANAGER",
      "USER" => "nom_du_groupe_ldap_USER"
    );

    $default_admin_username = "adminuid"; //set the first ldap uid used to login on this web site.

    # Adresse de contact apparaissant sur l'interface
    # Laisser vide pour que rien ne s'affiche
    $mail_support = "mailto:agimus-contact@univ.fr";

    # Activer l'export
    # L'export alimente la base, il faut installer pageres par ailleurs pour réaliser effectivement l'export
    # Le token que vous paramétrerez ici doit également être modifié dans le script de screenshot
    $export_actif = true;
    $export_token = "KDjhkjj77Sghd545JkHNdkkj";

    ///////////////////////////////////////
    // Basic Config of the phpCAS client //
    ///////////////////////////////////////

    // Full Hostname of your CAS Server
    $cas_host = 'cas.univ.fr';

    // Context of the CAS Server
    $cas_context = '/';

    // Port of your CAS server. Normally for a https server it's 443
    $cas_port = 443;

    $LDAP['HOST'] = 'ldaps://ldap.univ.fr:636';
    $LDAP['BIND_DN'] = 'cn=agimus,ou=system,dc=univ,dc=fr';
    $LDAP['BIND_PASSWORD'] = 'mdpAgimus';
    $LDAP['BASE_DN'] = "dc=univ,dc=fr";
    $LDAP['PEOPLE_BASE_DN'] = "ou=people,".$LDAP['BASE_DN'];
    $LDAP['PEOPLE_ATTRS'] = array("uid", "mail",  "group");
	// saisir les attributs en minuscule !!!
    $LDAP['GROUP_ATTRS'] = "group";


    // Set an UTF-8 encoding header for internation characters (User attributes)
    header('Content-Type: text/html; charset=utf-8');
?>
