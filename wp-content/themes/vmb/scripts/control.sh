#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# shellcheck disable=SC1091
source "${script_path}/config.sh"

file=${script_path:-0}/xml/listmodifiedportfolios.xml

startdate=$(date -d "-1 day" +%Y-%m-%d) 
enddate=$(date +'%Y-%m-%d') 

echo "Start date: $startdate"
echo "End date: $enddate"


xml='xml=<?xml version="1.0"?>
<request xmlns="http://fusionapi.traveltek.net/1.0/xsds"> 
    <auth username="'${ca_tt_username:-0}'" password="'${ca_tt_password:-0}'" />  
    <method action="listmodifiedportfolios" sitename="'${ca_tt_sitename:-0}'" sincelastchecked="1" startdate="'${startdate}'" enddate="'${enddate}'" /> 
</request>'

echo "xml = $xml"
curl -o "$file" -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/listmodifiedportfolios" \
		-H "Content-Type: application/x-www-form-urlencoded" \
		-d "$xml" 

mysql --login-path=local -N --execute="USE ${ca_db_name:-0};
LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix:-0}_portfolios 
ROWS IDENTIFIED BY '<portfolio>'
	(id,
	bookingreference,
	lastupdated);

DELETE FROM ${ca_db_table_prefix:-0}_portfolio_flight WHERE status = 'hidden';"