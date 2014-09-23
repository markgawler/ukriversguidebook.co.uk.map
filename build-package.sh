#!/bin/bash

echo "clean old packages"
rm packagefiles/*.zip

echo "Component, Creating Zip..."
cd com_ukrgbmap
zip -qr ../packagefiles/com_ukrgbmap .


echo "Plugin Creating Zip..."
cd ../plg_ukrgbmap
zip -qr ../packagefiles/plg_ukrgbmap .


echo "Library Creating Zip..."
cd ../lib_ukrgbgeo
zip -qr ../packagefiles/lib_ukrgbgeo .

echo "Done."
