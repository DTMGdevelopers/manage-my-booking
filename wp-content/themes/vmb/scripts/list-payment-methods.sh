#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# shellcheck disable=SC1091
source "${script_path}/functions.sh"

xml='xml=<?xml version="1.0"?>
  <request xmlns="http://fusionapi.traveltek.net/1.0/xsds">
    <auth username="'${ca_tt_username:-0}'" password="'${ca_tt_password:-0}'" /> 
    <method action="listpaymentmethods" sitename="'${ca_tt_sitename:-0}'"/>
  </request>'

file=$script_path/xml/listpaymentmethods.xml

curl -o "$file" -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/listpaymentmethods" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "$xml" 

sed -i 's/xmlns="http:\/\/fusionapi.traveltek.net\/1.0\/xsds"/ /g' "$file"

mysql --login-path=local --skip-column-names --local-infile --execute="USE ${ca_db_name:-0};
TRUNCATE TABLE ${ca_db_table_prefix:-0}_payment_methods;

LOAD XML LOCAL INFILE '$file' 
INTO TABLE ${ca_db_table_prefix:-0}_payment_methods 
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