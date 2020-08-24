#!/bin/bash

DIRECTORY_TO_COMPRESS="mobilize-america"
ZIPPED_FILE="mobilize-america.zip"

cd ../
echo "Change directory and move to new temp dir."
mv mobilize-america mobilize-america-temp
echo "Make temporary version of the plugin and copy desired files."
mkdir mobilize-america
cp -r mobilize-america-temp/classes mobilize-america
cp -r mobilize-america-temp/languages mobilize-america       
cp -r mobilize-america-temp/src mobilize-america      

cp mobilize-america-temp/*.php mobilize-america
cp mobilize-america-temp/README.txt mobilize-america
cp mobilize-america-temp/LICENSE.txt mobilize-america
cp mobilize-america-temp/composer.json mobilize-america

echo "Move into Plugin Dir to install composer deps."
cd mobilize-america 
echo "Install production composer deps"
# This requires jq: https://www.howtogeek.com/529219/how-to-parse-json-files-on-the-linux-command-line-with-jq/
if [ -s './composer.json' ]; then
    #Detect if there are composer dependencies
    echo "-Check composer dependencies..."
    if [ "$(uname)" == "Darwin" ]; then
		dep=$(cat "./composer.json" | jq 'has("require")')
	else
		dep=$(cat "./composer.json" | jq 'has(".require")')
	fi

    if [ "$dep" == 'true' ]; then
        echo "-Download clean composer dependencies..."
        composer update --no-dev # &> /dev/null
        echo "-Run composer dumpautoload -o"
        composer dumpautoload -o
    else
        rm -rf ./composer.json
    fi
fi 
echo "Move back up a level"
cd ../
echo "Make zip file."
zip -r "$ZIPPED_FILE" "$DIRECTORY_TO_COMPRESS"
echo $DIRECTORY_TO_COMPRESS "compressed as" $ZIPPED_FILE > /dev/null
echo "Remove temp file and changing directories."
rm -rf "$DIRECTORY_TO_COMPRESS"
echo "Rename to original directory name."
mv mobilize-america-temp mobilize-america
cd mobilize-america
echo "Zip file made and back home again."