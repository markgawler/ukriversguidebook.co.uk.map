#!/bin/bash

# Find the parent directory for this script, which should be the project root
PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"

rsync -av --exclude="favicon.ico" --exclude="index.html" "$PROJECT_ROOT"/ukrgb-river-app/dist/* "$PROJECT_ROOT"/com_ukrgbmap/site/view/map/



cd "$PROJECT_ROOT" || exit
echo "clean old packages"
rm ./packagefiles/*.zip

echo "Component, Creating Zip..."
cd com_ukrgbmap || exit
zip -qr ../packagefiles/com_ukrgbmap .

echo "Plugin Creating Zip..."
cd ../plg_ukrgbmap || exit
zip -qr ../packagefiles/plg_ukrgbmap .

echo "Library Creating Zip..."
cd ../lib_ukrgbgeo || exit
zip -qr ../packagefiles/lib_ukrgbgeo .

echo "Done."
