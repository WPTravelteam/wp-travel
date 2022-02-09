#!/bin/bash

# Version key/value should be on his own line
PACKAGE_VERSION=$(cat package.json \
  | grep version \
  | head -1 \
  | awk -F: '{ print $2 }' \
  | sed 's/[",]//g' \
  | tr -d '[[:space:]]')


echo "Checkout dev"
git checkout main
echo "*********************************"

echo "Assets Tasks"
yarn build:pre_release
echo "*********************************"

echo "State files"
git add --all
echo "*********************************"

echo "Commit files"
git commit -m "Changes for $PACKAGE_VERSION"
echo "*********************************"

echo "Push files"
git push origin main
echo "*********************************"

if [ $1 ]; then
  echo "Tag"
  git tag $PACKAGE_VERSION
  echo "*********************************"

  echo "Push tag"
  git push origin $PACKAGE_VERSION
  echo "*********************************"

fi
