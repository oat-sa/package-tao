#!/bin/bash

binDirectory="./bin";
destinationFile="$binDirectory/generis";
localFile="./generis/bin/generis";

if [[ ! -f "$localFile" ]]; then
  exit;
fi

if [[ ! -d "$binDirectory" ]]; then
  mkdir "$binDirectory";
fi

if [[ ! -f "$destinationFile" ]]; then
    cp "$localFile" "$destinationFile";
    chmod 775 "$destinationFile";
fi

