#!/bin/bash

script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
# shellcheck disable=SC1091
source "${script_path}/functions.sh"

#MARK: files and folders
# mkdir -p "${wordpress_path}"/log/
# mkdir -p "${wordpress_path}"/volume/
# mkdir -p "${wordpress_path}"/volume/images/
# mkdir -p "${wordpress_path}"/volume/images/small/
# mkdir -p "${script_path}"/csv
# mkdir -p "${script_path}"/xml
# mkdir -p "${script_path}"/log
# mkdir -p "${script_path}"/json

cat > "${script_path}"/xml/.htaccess << EOF
AuthUserFile ${script_path}/xml/.htpasswd
AuthType Basic
AuthName "DTMG restricted Area"
Require valid-user
EOF

cat > "${script_path}"/xml/.htpasswd << EOF
dtmg:\$apr1\$2t5hqcds\$03s0A.bWReM0O5KxCsRzn1
EOF

#MARK: - DB Connection (with 24h cache)
cache_dir="${script_path}/.cache"
mkdir -p "$cache_dir"
cache_file="${cache_dir}/ca_vars.cache"
rm -f "$cache_file"
cache_ttl=86400 # 24 hours in seconds

# Flush cache if requested
if [[ "$1" == "--flush-cache" ]]; then
  if [ -f "$cache_file" ]; then
    rm -f "$cache_file"
    echo "Cache flushed."
  else
    echo "No cache file to flush."
  fi
fi

# If cache exists and is fresh, source it and skip DB queries
if [ -f "$cache_file" ] && [ $(( $(date +%s) - $(stat -c %Y "$cache_file") )) -lt $cache_ttl ]; then
  # shellcheck source=/dev/null
  source "$cache_file"
else
    # Find WordPress path by locating wp-config.php
    wordpress_path="$(find "${HOME}" -type f -name "wp-config.php" -print0 | xargs -0 -r dirname | head -n 1)"

    echo "Script path: $script_path"
    echo "WordPress path: $wordpress_path"

    # Exit if wordpress_path is undefined or empty
    if [ -z "${wordpress_path}" ]; then
    echo "Error: wordpress_path is undefined. Exiting."
    exit 1
    fi
    # Query DB and write variables to cache file
    {
    ca_db_name="$(wp config get DB_NAME --path="${wordpress_path}" --skip-plugins --skip-themes )"
    ca_db_table_prefix="$(wp db prefix --path="${wordpress_path}" --skip-plugins --skip-themes | sed 's/.$//' )"

    echo "ca_db_name=\"$ca_db_name\""
    echo "ca_db_table_prefix=\"$ca_db_table_prefix\""
    echo "ca_db_connect=\"\${ca_db_connect:-'--login-path=local'}\""
    echo "ca_tt_currency=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'options_ca_tt_currency';")\""
    echo "ca_tt_endpoint=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'options_ca_tt_endpoint';")\""
    echo "ca_tt_endpoint_secure=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'options_ca_tt_endpoint_secure';")\""
    echo "ca_tt_password=\"$(mysql --login-path=local -N --default-character-set=utf8 --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'options_ca_tt_password';" | jq --raw-input --raw-output '. | @uri')\""
    echo "ca_tt_sid=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'options_ca_tt_sid';")\""
    echo "ca_tt_sitename=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'options_ca_tt_sitename';")\""
    echo "ca_tt_status=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT IF(option_value=1, 'Live', 'Test') FROM ${ca_db_table_prefix}_options WHERE option_name = 'options_ca_tt_status';")\""
    echo "ca_tt_username=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'options_ca_tt_username';")\""
    echo "ca_theme=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'template';")\""
    echo "ca_trading_name_whitelist=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'traveltek_trading_name_whitelist';")\""
    echo "ca_document_id=\"$(mysql --login-path=local -N --execute="USE $ca_db_name; SELECT option_value FROM ${ca_db_table_prefix}_options WHERE option_name = 'traveltek_document_id';")\""
    
    } > "$cache_file"
    # shellcheck source=/dev/null
    source "$cache_file"
fi

cat "$cache_file"