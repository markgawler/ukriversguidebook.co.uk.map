#!/bin/bash

# Find the parent directory for this script, which should be the project root
PROJECT_ROOT="$( cd "$( dirname "${BASH_SOURCE[0]}" )/.." &> /dev/null && pwd )"

cd "$PROJECT_ROOT" || exit
echo "Clean old packages"
rm ./packagefiles/*.zip



temp_dir=$(mktemp -d)
component_dir=$PROJECT_ROOT/com_ukrgbmap

# Make the river-app Vue application and plugin package
echo "Build Vue App and Plugin Creating Zip..."
"$PROJECT_ROOT"/tools/build-plugin-package.sh

# prep for building component
rsync -a --exclude=".idea" "$component_dir" "$temp_dir"
rsync -a --exclude="favicon.ico" --exclude="index.html" "$PROJECT_ROOT"/ukrgb-river-app/dist/* "$temp_dir"/com_ukrgbmap/site/view/map/

# Make the Component .zip package
echo "Component, Creating Zip..."
cd "$temp_dir" || exit
zip -qr "$PROJECT_ROOT/packagefiles/com_ukrgbmap" .
rm -rf "$temp_dir"
cd "$PROJECT_ROOT" || exit


# Make the Library .zip package
echo "Library Creating Zip..."
cd "$PROJECT_ROOT"/lib_ukrgbgeo || exit
zip -qr ../packagefiles/lib_ukrgbgeo .

echo "Done."
