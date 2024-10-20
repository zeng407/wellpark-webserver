#!/bin/bash

# Get the directory of the current script
SCRIPT_DIR=$(dirname "$0")

# Define source and destination directories
SOURCE_DIR="$SCRIPT_DIR/crawler/parkinfo/output"
DEST_DIR="$SCRIPT_DIR/webserver/storage/app/private/parkinfo"

# Create destination directory if it doesn't exist
mkdir -p "$DEST_DIR"

# Move JSON files from source to destination
mv "$SOURCE_DIR"/*.json "$DEST_DIR"

# Print a message indicating completion
echo "Files moved from $SOURCE_DIR to $DEST_DIR"