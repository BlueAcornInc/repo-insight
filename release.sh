#!/bin/bash

VERSION=$1
SEMVER_REGEX="^(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)\.(0|[1-9][0-9]*)(\-[0-9A-Za-z-]+(\.[0-9A-Za-z-]+)*)?(\+[0-9A-Za-z-]+(\.[0-9A-Za-z-]+)*)?$"
BOX_PATH=`which box.phar 2>/dev/null`


throw () {
  printf "\nERROR:\n  $1\n"
  exit 1;
}

if [[ ! "$VERSION" =~ $SEMVER_REGEX ]]; then
  throw "Please pass the semantic version of this release (e.g. 0.1.1)" 
fi


if [ ! -e "$BOX_PATH" ]; then
  BOX_PATH=`which box 2>/dev/null`
  if [ ! -e "$BOX_PATH" ]; then
    throw "ERROR: box not found!"
  fi
fi


printf "\nTagging Release as $VERSION\n\n"

$BOX_PATH build
if [ ! $? -eq 0 ]; then
  throw "box failed to build phar"
fi

cp repo-insight.phar releases/repo-insight-${VERSION}.phar
if [ ! $? -eq 0 ]; then
  throw "unable to copy release"
fi


HASH=$(openssl sha1 repo-insight.phar | awk '{print $2}')

sed "s/@@version@@/$VERSION/g" manifest.json.template > manifest.json
sed -i "s/@@sha1@@/$HASH/g" manifest.json
git add manifest.json
if [ ! $? -eq 0 ]; then
  throw "git failed adding release"
fi

git add repo-insight.phar
git add releases/repo-insight-${VERSION}.phar
if [ ! $? -eq 0 ]; then
  throw "git failed adding release"
fi

git tag $VERSION 
if [ ! $? -eq 0 ]; then
  throw "git failed tagging release"
fi


git commit -m "Bump to version ${VERSION}"
git push origin --tags


