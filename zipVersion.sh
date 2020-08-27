#!/bin/bash

DIRECTORY_TO_COMPRESS="mz-mobilize-america-interface"
ZIPPED_FILE="mz-mobilize-america-interface.zip"

cd ../
echo "Change directory and move to new temp dir."
mv mz-mobilize-america-interface mz-mobilize-america-temp
echo "Make temporary version of the plugin and copy desired files."
mkdir mz-mobilize-america-interface
cp -r mz-mobilize-america-temp/classes mz-mobilize-america-interface
cp -r mz-mobilize-america-temp/languages mz-mobilize-america-interface       
cp -r mz-mobilize-america-temp/src mz-mobilize-america-interface      

cp mz-mobilize-america-temp/*.php mz-mobilize-america-interface
cp mz-mobilize-america-temp/README.txt mz-mobilize-america-interface
cp mz-mobilize-america-temp/LICENSE.txt mz-mobilize-america-interface
cp mz-mobilize-america-temp/composer.json mz-mobilize-america-interface

echo "Move into Plugin Dir to install composer deps."
cd mz-mobilize-america-interface 
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
mv mz-mobilize-america-temp mz-mobilize-america-interface
cd mz-mobilize-america-interface
echo "Zip file made and back home again."