#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
sed -i -e 's/\r$//' $script_path/config.sh
source $script_path/config.sh
source $script_path/functions.sh

$script_path/control-tables.sh ${ca_db_table_prefix}_payment_methods

xml='xml=<?xml version="1.0"?>
  <request xmlns="http://fusionapi.traveltek.net/1.0/xsds">
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" /> 
    <method action="listpaymentmethods" sitename="'${ca_tt_sitename:-0}'"/>
  </request>'

file=$script_path/xml/listpaymentmethods.xml

curl -o $file -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/listpaymentmethods" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "$xml" 

sed -i 's/xmlns="http:\/\/fusionapi.traveltek.net\/1.0\/xsds"/ /g' $file

mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
TRUNCATE TABLE "$ca_db_table_prefix"_payment_methods;

LOAD XML LOCAL INFILE '$file' 
INTO TABLE "$ca_db_table_prefix"_payment_methods 
ROWS IDENTIFIED BY '<paymentmethod>'
  (availableto,
  bankreconciliation,
  chargetype,
  chargetypebank,
  chargevalue,
  chargevaluebank,
  creditcardcode,
  groupname,
  gstoncharges,
  hidden,
  id,
  localid,
  lockedcharge,
  membershippoints,
  merchantfeejournal,
  name,
  needpermission,
  nettfees,
  type);"