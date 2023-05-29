#!/bin/bash

# Check if the filepath argument is provided
if [ $# -ne 1 ]; then
  echo "Usage: $0 <filepath>"
  exit 1
fi

# Set the Git credentials for this script
GIT_USER="your_username"
GIT_EMAIL="your_email@example.com"

# Set the filepath argument as a variable
FILEPATH="$1"

# Check if the file exists
if [ ! -f "$FILEPATH" ]; then
  echo "File not found: $FILEPATH"
  exit 1
fi

# Configure Git credentials for this script
git config user.name "typo-static-vs"
git config user.email "dev.tobeh@gmail.com"

# Commit and push the specified file
git add "$FILEPATH"
git commit -m "Automatic commit of $FILEPATH"
git push

# Reset the Git credentials
git config --unset user.name
git config --unset user.email

echo "File committed and pushed successfully: $FILEPATH"