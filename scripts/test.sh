#!/bin/bash

script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
# shellcheck disable=SC1091
source "${script_path}/functions.sh"

tradingname="MyCruises"
ca_trading_name_whitelist="Explorations by Norwegian,My Cruises Exclusive Luxury Collection,My Cruises Expedition Collection,MyCruises,My Cruises River Collection,My Cruises Sales Centre - Cruise,My Cruises Sales Centre - Touring,My Cruises Touring Collection"

if [[ ",${ca_trading_name_whitelist}," != *",${tradingname},"* ]]; then
    echo "Trading name '${tradingname}' is not whitelisted. Exiting."
    exit 1
fi

echo "Trading name '${tradingname}' is whitelisted."
