#!/bin/bash

DIRECTORY_TO_COMPRESS="mobilize-america"
ZIPPED_FILE="mobilize-america.zip"

cd ../
echo "Changing directory and moving to new temp dir."
mv mobilize-america mobilize-america-temp
echo "Make temporary version of the plugin and copying desired files."
mkdir mobilize-america
cp -r mobilize-america-temp/classes mobilize-america
cp -r mobilize-america-temp/languages mobilize-america       
cp -r mobilize-america-temp/src mobilize-america      
cp -r mobilize-america-temp/vendor mobilize-america
cp mz-mindbody-api-temp/*.php mobilize-america
cp mz-mindbody-api-temp/README.txt mobilize-america
cp mz-mindbody-api-temp/LICENSE.txt mobilize-america
echo "Files copied. Making zip file."
zip -r "$ZIPPED_FILE" "$DIRECTORY_TO_COMPRESS"
echo $DIRECTORY_TO_COMPRESS "compressed as" $ZIPPED_FILE > /dev/null
echo "Removing temp file and changing directories."
rm -r "$DIRECTORY_TO_COMPRESS"
echo "Renaming to original directory name."
mv mobilize-america-temp mobilize-america
cd mobilize-america
echo "Zip file made and back home again."