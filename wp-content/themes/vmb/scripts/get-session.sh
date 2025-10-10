#!/bin/bash

script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# shellcheck disable=SC1091
source "${script_path}/functions.sh"

bookingid=${1}
outstanding=${2}

file=$script_path/xml/getportfolio-${bookingid}.xml
#sid=$(xmlstarlet sel -t -m "/response/results/result/bookingdetails/@sid" -v . -n $file )
# #sid=$(mysql --login-path=local --skip-column-names --local-infile --execute="USE ${ca_db_name:-0}; SELECT sid FROM ${ca_db_table_prefix:-0}_portfolio_details  WHERE bookingid = ${bookingid};")

#echo "Booking ID: ${bookingid} - SID: ${sid}"

if [ -z "${outstanding}" ];then
xml='xml=<?xml version="1.0"?>
  <request>
    <auth username="'${ca_tt_username:-0}'" password="'${ca_tt_password:-0}'" /> 
    <method action="createsession" sitename="'${ca_tt_sitename:-0}'" status="Live" sid="'${sid:-0}'" currency="'${ca_tt_currency:-0}'" />
  </request>'
else
xml='xml=<?xml version="1.0"?>
  <request>
    <auth username="'${ca_tt_username:-0}'" password="'${ca_tt_password:-0}'" /> 
    <method action="createsession" sitename="'${ca_tt_sitename:-0}'" status="Live" sid="'${sid:-0}'" currency="'${ca_tt_currency:-0}'" outstanding="'${outstanding}'" />
  </request>'
fi

#echo "$xml"

file="${script_path}/xml/createsession-${bookingid}.xml"

curl -s -o "$file" -X POST --url "https://fusionapi.traveltek.net/0.9/interface.pl"  \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "$xml" 

sessionkey=$(xmlstarlet sel -t -m "/response/request/method/@sessionkey" -v . -n "$file" )
echo "${sessionkey}"

mysql --login-path=local --skip-column-names --local-infile --execute="USE ${ca_db_name:-0};
DELETE FROM ${ca_db_table_prefix:-0}_portfolio_session WHERE bookingid = ${bookingid};

INSERT INTO  ${ca_db_table_prefix:-0}_portfolio_session
(sessionkey,dateadded,bookingid)
VALUE 
('${sessionkey}',NOW(),${bookingid});"