#!/bin/bash

script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
# shellcheck disable=SC1091
source "${script_path}/functions.sh"

tradingname="MyCruises"

if [[ ",${ca_trading_name_whitelist}," != *",${tradingname},"* ]]; then
    echo "Trading name '${tradingname}' is not whitelisted. Exiting."
    exit 1
fi

echo "Trading name '${tradingname}' is whitelisted."
