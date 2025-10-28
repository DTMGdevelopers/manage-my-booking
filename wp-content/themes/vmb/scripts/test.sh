#!/bin/bash

script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
# shellcheck disable=SC1091
source "${script_path}/functions.sh"

file=$script_path/json/token.json
echo "ca_tt_username = $ca_tt_username"
echo "ca_tt_password = $ca_tt_password"

ca_tt_auth_b64="$(echo -n "${ca_tt_username}:${ca_tt_password}" | base64)"

mkdir -p "${script_path}/json"

curl -s -o "$file" -X POST --url "https://fusionapi.traveltek.net/2.0/json/token.pl" \
		-H  "Content-Type: application/x-www-form-urlencoded" \
		-H  "Authorization: Basic ${ca_tt_auth_b64}" \
		--data-urlencode "grant_type=client_credentials" \
		--data-urlencode "scope=docdownload"

cat "$file"
token=$(jq -r '.access_token' "$file")
echo "Token: $token"
