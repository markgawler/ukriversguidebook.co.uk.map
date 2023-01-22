#!/bin/bash
# Script to update most of the joomla components files without installing the package. Install the Joomla package at
# least once before using this script as set the file permissions of the destination folders so the script can write
# there.
#
# todo: language files
# todo: Joomla Plugin files (low priority, this will need to update the .php file with the name of the Vue app build).
# todo: Build the Vue app when it changes (low priority)

PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"

component="com_ukrgbmap"

source="$PROJECT_ROOT/$component"
admin_src="$source/admin"
site_src="$source/site"
app_src="$PROJECT_ROOT/ukrgb-river-app/dist/assets"

admin_dest="$PROJECT_ROOT/docker/www/administrator/components/$component"
site_dest="$PROJECT_ROOT/docker/www/components/$component"

function sync {
    if [[ $dir == $site_src* ]]; then
        rsync -av --no-o --no-g --no-perms --quiet --exclude="language" "$site_src/" "$site_dest/"
    elif  [[ $dir == $admin_src* ]]; then
        rsync -a --no-o --no-g --no-perms --quiet "$PROJECT_ROOT/$component/ukrgbmap.xml" "$admin_dest/"
        rsync -av --no-o --no-g --no-perms --quiet --exclude="language" "$admin_src/" "$admin_dest/"
    elif  [[ $dir == $app_src* ]]; then
        rsync -av --no-o --no-g --no-perms --quiet "$app_src/" "$site_dest/view/map/assets"
    fi
}

# Watch for changes
while inotifywait -q -r -e modify,create,delete "$source" "$app_src" | 
    while read -r dir action file; do 
        echo "$action $dir$file"
        if [ "$action" == "DELETE" ]; then
            sleep 1
        fi
        sync "$dir"
    done
do 
    :    
done


