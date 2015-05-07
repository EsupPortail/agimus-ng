#!/bin/bash

# Script permettant d'importer un dossier de visualisations ou de dashboards dans Kibana.
# Vérifier de bien avoir elasticsearch en route et un index .kibana
# Usage : ./import_kibana.sh /path/to/folder visu|dash

if [ $# -ne 2 ] || [ ! -d "$1" ]
then
    echo "$1 : Folder containing the files to import in kibana."
    echo "$2 : Possible values : dash (to import dashboards), visu (to import visualizations)"
    echo "Usage : ./import_kibana.sh /path/to/folder visu|dash"
    exit 1
else
    if [ $2 == "visu" ]
    then
        type="visualization"
    elif [ $2 == "dash" ]
    then
        type="dashboard"
    else
        echo "$2 : Possible values : dash (to import dashboards), visu (to import visualizations)"
        echo "Usage : ./import_kibana.sh /path/to/folder visu|dash"
        exit 1
    fi

    ls $1 | while read file
    do
        name=`echo "$file" | cut -d. -f1`
        curl -XPUT "http://localhost:9200/.kibana/$type/$name" -d @$1/$file
    done
fi
exit 0