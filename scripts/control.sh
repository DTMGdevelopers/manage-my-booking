#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
sed -i -e 's/\r$//' $script_path/config.sh
source $script_path/config.sh
source $script_path/functions.sh

#CHECK THIS SCRIPT ISNT ALREADY RUNNING 
if [[ "`pidof -x $(basename $0) -o %PPID`" ]]; then
	echo "This script is already running with PID `pidof -x $(basename $0) -o %PPID`" #> $script_path/log/search.log
	#echo "This script is already running with PID `pidof -x $(basename $0) -o %PPID`" 
	exit
fi

file=$script_path/xml/listmodifiedportfolios.xml

startdate=$(date --date="${dataset_date} -1 day" +%Y-%m-%d) 
enddate=$(date +'%Y-%m-%d') 

xml='xml=<?xml version="1.0"?>
<request xmlns="http://fusionapi.traveltek.net/1.0/xsds"> 
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" />  
    <method action="listmodifiedportfolios" sitename="ignite.site.traveltek.net" sincelastchecked="1" startdate="'${startdate}'" enddate="'${enddate}'" /> 
</request>'

curl -o $file -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/listmodifiedportfolios" \
		-H "Content-Type: application/x-www-form-urlencoded" \
		-d "$xml" 

mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix}_portfolios 
ROWS IDENTIFIED BY '<portfolio>'
	(id,
	bookingreference,
	lastupdated);

DELETE FROM ${ca_db_table_prefix}_portfolio_flight WHERE status = 'hidden';"