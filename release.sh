#!/bin/bash

# This file automates the release process based on the tag of the current commit
#
# 1) Tag current version: "git tag x.y.z", where x is the major, y the minor
#   and z the patch version number. For example: "git tag 2.0.0"
#
# 2) Build release file: "sh release.sh". For the example above, this will build
#   "simplehtmldom_2_0_0.zip"

tag=$(git tag -l --points-at HEAD)

if [ -z "$tag" ]; then
  echo "The current commit is not tagged!"
  echo "Insert valid tag name or press Ctrl+C to abort."
  read -p "Format: Major.Minor.Patch[-Suffix]: " tag
  if [ -z "$tag" ]; then
    echo "No tag name provided."
    exit
  fi;
  $(git tag ${tag})
fi;

# Check if the tag follows https://semver.org/
version="$(echo ${tag} | cut -d'-' -f1)"
major="$(echo ${version} | cut -d'.' -f1)"
minor="$(echo ${version} | cut -d'.' -f2)"
patch="$(echo ${version} | cut -d'.' -f3)"
suffix="$(echo ${tag} | cut -d'-' -f2)"

# git tag could return an error
tag=$(git tag -l --points-at HEAD)

if [ -z "$tag" ]; then
  echo "Something went wrong!"
  exit
fi;

echo "Building release for ${tag}..."

if [ -z "$major" ]; then echo "Major version is missing in ${tag}"; fi;
if [ -z "$minor" ]; then echo "Minor version is missing in ${tag}"; fi;
if [ -z "$patch" ]; then echo "Patch version is missing in ${tag}"; fi;

if [ -z "$major" ] || [ -z "$minor" ] || [ -z "$patch" ]; then
  echo "Aborting script!"
  exit
fi;

# Archive file
prefix="simplehtmldom_"
version=$(echo "$tag" | tr . _)

# Keyword substitution in files
marker="\\\$Rev\\\$"
replacement="Rev. $tag ($(git rev-list --count HEAD))"

# Build archive
if [ "$version" ]; then
  # Inject version information to all files (limit to file type!)
  find . -name '*.php' -exec sed -i -e "s/$marker/$replacement/g" {} \;;
  find . -name '*.htm' -exec sed -i -e "s/$marker/$replacement/g" {} \;;
  # Create stash commit (otherwise git archive won't work)
  stash=$(git stash create);
  git archive --format=zip --output="$prefix$version".zip --worktree-attributes "$stash";
  # Cleanup
  git checkout .;
  git gc --prune;
fi;