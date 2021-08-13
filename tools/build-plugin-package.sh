#!/bin/bash

PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"
temp_dir=$(mktemp -d)

CSS=$PROJECT_ROOT/ukrgb-river-app/dist/css/
JS=$PROJECT_ROOT/ukrgb-river-app/dist/js/
plugin_file=$temp_dir/plg_ukrgbmap/ukrgbmap.php  # This file is edited to insert the VUE App ids
plugin_dir=$PROJECT_ROOT/plg_ukrgbmap

function get_ids
{
    path=$1
    sufix=$2

    local app_id
    local chunk_id
    for f in "$path"/*."$sufix" ;do
        file=$(basename "$f" ."$sufix")
        case $file in
        app*)
            if [ -n "$app_id" ]; then
                echo "Multiple app"
            fi
            app_id=${file/app./}
            ;;

        chunk-vendors*)
            if [ -n "$chunk_id" ]; then
                echo "Multiple chuncks"
            fi
            chunk_id=${file/chunk-vendors./}
            ;;
        *)
            echo "Unexpected file: $file"
        esac
    done

    echo "$app_id $chunk_id"
}

function set_var_value
{
    local var=$1
    local value=$2
    local file=$3
    sed -i "/^\W*\$$var\W*=/ s/\"[0-9a-z]*\"/\"$value\"/" "$file"
}

# copy the plugin files
rsync -av --exclude=".idea" "$plugin_dir" "$temp_dir"

res=$(get_ids "$CSS" "css")
app=${res%% *}
chunk=${res##* }

set_var_value "app_css" "$app" "$plugin_file"
set_var_value "chunk_vendors_css" "$chunk" "$plugin_file"

res=$(get_ids "$JS" "js")
app=${res%% *}
chunk=${res##* }

set_var_value "app_js" "$app" "$plugin_file"
set_var_value "chunk_vendors_js" "$chunk" "$plugin_file"

# Make the .zip packa
cd "$temp_dir" || exit
zip -qr "$PROJECT_ROOT/packagefiles/plg_ukrgbmap" .
rm -rf "$temp_dir"