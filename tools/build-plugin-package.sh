#!/bin/bash

PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"
temp_dir=$(mktemp -d)
echo "$temp_dir"
cd "$PROJECT_ROOT" || exit

ASSETS=$PROJECT_ROOT/ukrgb-river-app/dist/assets
plugin_file=$temp_dir/plg_ukrgbmap/ukrgbmap.php  # This file is edited to insert the VUE App ids
plugin_dir=$PROJECT_ROOT/plg_ukrgbmap

function get_ids
{
    path=$1
    sufix=$2

    local app_id
    local chunk_id
    local index_id
    for f in "$path"/*."$sufix" ;do
        file=$(basename "$f" ."$sufix")
        case $file in
        app*)
            if [ -n "$app_id" ]; then
                echo "Multiple app" > /dev/tty
            fi
            app_id=${file/app./}
            ;;

        chunk-vendors*)
            if [ -n "$chunk_id" ]; then
                echo "Multiple chuncks" > /dev/tty
            fi
            chunk_id=${file/chunk-vendors./}
            ;;
        index-*)
            if [ -n "$index_id" ]; then
                echo "Multiple indexes" > /dev/tty
            fi
            index_id=${file/index-./}
            ;;
        *)
            echo "Unexpected file: $file" > /dev/tty
        esac
    done

    echo "$app_id $chunk_id $index_id"
}

function set_var_value
{
    #echo "Params:" >/dev/tty
    #echo "$@" > /dev/tty
    local var=$1
    local value=$2
    local file=$3
    sed -i "/^\W*\$$var\W*=/ s/\"[0-9a-z]*\"/\"$value\"/" "$file"
}

# copy the plugin files
rsync -a --exclude=".idea" "$plugin_dir" "$temp_dir"
res=$(get_ids "$ASSETS" "css")
val=${res##* }  # trim leading spaces
set_var_value "index_css" "$val" "$plugin_file"

res=$(get_ids "$ASSETS" "js")
val=${res##* }  # trim leading spaces
set_var_value "index_js" "$val" "$plugin_file"


# Make the .zip packa
cd "$temp_dir" || exit
zip -qr "$PROJECT_ROOT/packagefiles/plg_ukrgbmap" .
rm -rf "$temp_dir"
cd "$PROJECT_ROOT" || exit