#!/bin/bash
# Script to update most of the joomla components files without installing the package. Install the Joomla package at
# least once before using this script as set the file permissions of the destination folders so the script can write
# there. 
#
# todo: language files
# todo: Build the Vue app when it changes (low priority)

PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"
WEB_ROOT="$PROJECT_ROOT/docker/www"

component="com_ukrgbmap"
plugin="ukrgbmap"

source="$PROJECT_ROOT/$component"
admin_src="$source/admin"
site_src="$source/site"
app_src="$PROJECT_ROOT/ukrgb-river-app/dist/assets"

admin_dest="$WEB_ROOT/administrator/components/$component"
site_dest="$WEB_ROOT/components/$component"
plg_dest="$WEB_ROOT/plugins/content/$plugin"

function sync {
    dir=$1
    sleep 1
    if [[ $dir == $site_src* ]]; then
        rsync -av --no-o --no-g --no-perms --delete --exclude="language" "$site_src/" "$site_dest/"
    elif  [[ $dir == $admin_src* ]]; then
        rsync -a --no-o --no-g --delete --no-perms  "$PROJECT_ROOT/$component/ukrgbmap.xml" "$admin_dest/"
        rsync -av --no-o --no-g --delete --no-perms  --exclude="language" "$admin_src/" "$admin_dest/"
    elif  [[ $dir == $app_src* ]]; then
        rsync -av --no-o --no-g --no-perms --delete  "$app_src/" "$site_dest/view/map/assets"
        unzip -jo "$PROJECT_ROOT"/packagefiles/plg_"$plugin".zip plg_"$plugin"/"$plugin".* -d "$plg_dest/"
    fi
}

while [[ $# -gt 0 ]]; do
	key=$1
	case $key in
		--sync)
            sync "$site_src/"
            sync "$admin_src/"
            sync "$app_src/"
            exit
			;;

		--reset-permisions)
            sudo chown -R www-data:www-data "$WEB_ROOT"
            exit
            ;;

        --set-permisions)
            user=$(id --user --name)
            group=$(id --group --name)
            sudo chown -R "$user":"$group" "$admin_dest"
            sudo chown -R "$user":"$group" "$site_dest"
            sudo chown -R "$user":"$group" "$plg_dest"
            exit
        ;;

		--help)
			echo "  --sync              Forces syncing of all folders"
			echo "  --reset-permisions  Reset file permisions back to www-data ownership"
			echo "  --set-permisions    Set the permisions to curent user in prep for file sync"
			echo ""
			exit
			;;

		*)  # unknown option
			echo "$0: unrecognised option '$key'"
			echo "Try '$0 --help' for more information."
			exit
			;;
	esac
done

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


