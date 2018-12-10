#!/bin/bash

# This file automates the release process based on the tag of the current commit
#
# 1) Tag current version: "git tag x.y", where x is the major and y the minor
#   version number. For example: "git tag 1.6"
#
# 2) Build release file: "sh release.sh". For the example above, this will build
#   "simplehtmldom_1_6.zip"

tag=$(git tag -l --points-at HEAD)

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
  # Clenup
  git checkout .;
  git gc --prune;
else
  echo "Your commit is not tagged!";
fi;