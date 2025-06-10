#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
sed -i -e 's/\r$//' $script_path/config.sh
source $script_path/config.sh
source $script_path/functions.sh

bookingid=${1}
outstanding=${2}


# file=$script_path/xml/getportfolio-${bookingid}.xml
# sid=$(xmlstarlet sel -t -m "/response/results/result/bookingdetails/@sid" -v . -n $file )
# #sid=$(mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name"; SELECT sid FROM ${ca_db_table_prefix}_portfolio_details  WHERE bookingid = ${bookingid};")

if [ -z "${outstanding}" ];then
xml='xml=<?xml version="1.0"?>
  <request>
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" /> 
    <method action="createsession" sitename="ignite.site.traveltek.net" status="Live" sid="34534" currency="AUD" />
  </request>'
else
xml='xml=<?xml version="1.0"?>
  <request>
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" /> 
    <method action="createsession" sitename="ignite.site.traveltek.net" status="Live" sid="34534" currency="AUD" outstanding="'${outstanding}'" />
  </request>'
fi

file=$script_path/xml/createsession-${bookingid}.xml

curl -s -o $file -X POST --url "https://fusionapi.traveltek.net/0.9/interface.pl" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "$xml" 

sessionkey=$(xmlstarlet sel -t -m "/response/request/method/@sessionkey" -v . -n $file )
echo ${sessionkey}

mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
DELETE FROM ${ca_db_table_prefix}_portfolio_session WHERE bookingid = ${bookingid};

INSERT INTO  ${ca_db_table_prefix}_portfolio_session
(sessionkey,dateadded,bookingid)
VALUE 
('${sessionkey}',NOW(),${bookingid});"