#!/bin/bash
bash bash/pre_release.sh

PACKAGE_NAME=$(cat package.json \
  | grep name \
  | head -1 \
  | awk -F: '{ print $2 }' \
  | sed 's/[",]//g' \
  | tr -d '[[:space:]]')
SVN_URL=https://plugins.svn.wordpress.org/$PACKAGE_NAME

# Version key/value should be on his own line
PACKAGE_VERSION=$(cat package.json \
  | grep version \
  | head -1 \
  | awk -F: '{ print $2 }' \
  | sed 's/[",]//g' \
  | tr -d '[[:space:]]')

FILE=../../config/login.json
if [ ! -f "$FILE" ]; then
    printf "\n${RED}There is no login file.${NC}\n\n"
    exit 1
fi


SVN_USERNAME=$(cat ${FILE} \
  | grep username \
  | head -1 \
  | awk -F: '{ print $2 }' \
  | sed 's/[",]//g' \
  | tr -d '[[:space:]]')

SVN_PASSWORD=$(cat ${FILE} \
  | grep password \
  | head -1 \
  | awk -F: '{ print $2 }' \
  | sed 's/[",]//g' \
  | tr -d '[[:space:]]')

# Copy files from SVN URL
echo "Pull from SVN"
svn co $SVN_URL svn_folder
echo "*********************************"
echo ""

# Delete files from svn trunk
echo "Remove files from trunk"
rm -rf ./svn_folder/trunk/*
#rm -rf ./svn_folder/tags/*
rm -rf ./svn_folder/assets/*
echo "*********************************"
echo ""

# Copy new set of files to trunk
echo "copy files to trunk"
rsync -a ./bundle/$PACKAGE_VERSION/$PACKAGE_NAME/ ./svn_folder/trunk
echo "*********************************"
echo ""

# Create tag folder
echo "Create new tag folder"
mkdir ./svn_folder/tags/$PACKAGE_VERSION;
echo "*********************************"
echo ""

# Copy new set of files to tag
echo "copy files to tag"
rsync -a ./bundle/$PACKAGE_VERSION/$PACKAGE_NAME/ ./svn_folder/tags/$PACKAGE_VERSION
echo "*********************************"
echo ""

# Copy .org assets to assets
echo "Copy .org assets to assets"
rsync -a ./org_assets/* ./svn_folder/assets
echo "*********************************"
echo ""

# Go to trunk folder
echo "Files that would be pushed"
cd ./svn_folder
# exec bash
echo "*********************************"
echo ""

# Stage all files
echo "Adding modified files"
svn add --force * --auto-props --parents --depth infinity -q
echo "*********************************"
echo ""

# https://stackoverflow.com/questions/11066296/svn-delete-removed-files
#if [ $( svn status | sed -e '/^!/!d' -e 's/^!//' ) ]; then
    echo "Removing files"
#   svn rm $( svn status | sed -e '/^!/!d' -e 's/^!//' )
    svn status | grep '^!' | awk '{$1=""; print " --force \""substr($0,2)"@\"" }' | xargs svn delete
    echo "*********************************"
    echo ""

#fi

#svn status
echo "*********************************"

# Commit changes
echo "Sending to SVN"
svn ci -m 'New Changes !!' --username "$SVN_USERNAME" --password $SVN_PASSWORD
echo "*********************************"
echo ""
