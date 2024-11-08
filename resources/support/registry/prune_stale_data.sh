#!/bin/bash

set -e

echo "Pruning stale data"

/bin/registry garbage-collect /etc/docker/registry/config.yml

echo "Pruning empty repositories"

for repo_name in /var/lib/registry/docker/registry/v2/repositories/*; do
  # Check if it's a directory
  if [ -d "$repo_name" ]; then
    # Check if the _manifests/tags directory is empty
    if [ -z "$(ls -A "$repo_name/_manifests/tags/")" ]; then
      # If the directory is empty, remove the repository directory
      echo "Removing repository $repo_name as its _manifests/tags directory is empty."
      rm -rf "$repo_name"
    else
      # If the directory is not empty, do nothing
      echo "Repository $repo_name has tags. Skipping removal."
    fi
  fi
done
