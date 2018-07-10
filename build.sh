#!/bin/sh

rm -rf {vendor,*.zip}
composer install --no-dev
cp -ar vendor src/
cd src
zip -r ../enable-svg-uploads$1.zip .
rm -rf vendor
cd ..
