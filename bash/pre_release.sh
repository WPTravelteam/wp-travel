#!/bin/bash

VERSION=$1

echo "Checkout dev"
git checkout dev
echo "*********************************"

echo "Assets Tasks"
# grunt pre_release
yarn build:pre_release
echo "*********************************"

echo "State files"
git add --all
echo "*********************************"

echo "Commit files"
git commit -m "Changes for $VERSION"
echo "*********************************"

echo "Push files"
git push origin dev
echo "*********************************"

if [ $1 ]; then
  echo "Tag"
  git tag $VERSION
  echo "*********************************"

  echo "Push tag"
  git push origin $VERSION
  echo "*********************************"

  echo "Checkout to master"
  #git checkout master
  echo "*********************************"

  echo "Pull master"
  #git pull origin master
  echo "*********************************"

  echo "Merge with dev"
  #git merge dev
  echo "*********************************"

  echo "Push master"
  #git push origin master
  echo "*********************************"

  echo "Checkout dev"
  #git checkout dev
  echo "*********************************"

fi
