#!/bin/bash

#SVN_URL=https://plugins.svn.wordpress.org/wp-travel

# Copy files from SVN URL
echo "Pull from SVN"
svn co $SVN_URL build
echo "*********************************"
echo ""

# Delete files from svn trunk
echo "Remove files from trunk"
rm -rf ./build/trunk/*
#rm -rf ./build/tags/*
rm -rf ./build/assets/*
echo "*********************************"
echo ""

# Copy new set of files to trunk
echo "copy files to trunk"
rsync -a --exclude ".git*" --exclude bash --exclude build --exclude modules --exclude inc/class-modules.php  --exclude org_assets  --exclude node_modules --exclude .editorconfig --exclude Gruntfile.js --exclude package.json --exclude package-lock.json --exclude push_dot_org.sh --exclude README.md --exclude .sass-cache     ./ ./build/trunk
echo "*********************************"
echo ""

#!/bin/bash          
CURRENT_TAG=3.0.3

# Create tag folder
echo "Create new tag folder"
mkdir ./build/tags/$CURRENT_TAG;
echo "*********************************"
echo ""


# Copy new set of files to tag
echo "copy files to tag"
rsync -a --exclude ".git*" --exclude bash --exclude build --exclude modules --exclude inc/class-modules.php  --exclude org_assets  --exclude node_modules --exclude .editorconfig --exclude Gruntfile.js --exclude package.json --exclude package-lock.json --exclude push_dot_org.sh --exclude README.md  --exclude .sass-cache     ./ ./build/tags/$CURRENT_TAG
echo "*********************************"
echo ""

# Copy .org assets to assets
echo "Copy .org assets to assets"
rsync -a ./org_assets/* ./build/assets
echo "*********************************"
echo ""

# Go to trunk folder
echo "Files that would be pushed"
cd build
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
svn ci -m 'New Changes !!' --username "$SVN_USER" --password $SVN_PASS
echo "*********************************"
echo ""
