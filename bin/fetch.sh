#!/bin/sh
DIR="$(dirname "$0")"
STATUS=$(curl -s -w "%{http_code}" -o /dev/null -m 1 -I "$SOURCE_API_ENDPOINT")
if [ "$STATUS" -eq 200  ]; then
  curl "$SOURCE_API_ENDPOINT" > "$DIR/../data/planner-items-search.json"
fi
