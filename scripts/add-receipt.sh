#!/bin/bash

script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# shellcheck disable=SC1091
source "${script_path}/config.sh"

bookingid=${1}

file=$script_path/xml/book-$bookingid.xml
cardtype=$(xmlstarlet sel -t -m "/transaction/cardtype" -v . -n "${file}" )
amount=$(xmlstarlet sel -t -m "/transaction/amount" -v . -n "${file}" )
authcode=$(xmlstarlet sel -t -m "/transaction/authcode" -v . -n "${file}" )
reference=$(xmlstarlet sel -t -m "/transaction/reference" -v . -n "${file}" )
handlingfee=$(xmlstarlet sel -t -m "/transaction/handlingfee" -v . -n "${file}" )
transactionref=$(xmlstarlet sel -t -m "/transaction/transactionref" -v . -n "${file}" )
cardno=$(xmlstarlet sel -t -m "/transaction/cardno" -v . -n "${file}" )

paymentmethodid=$(mysql --login-path=local --skip-column-names --local-infile --execute="USE ${ca_db_name:-0};
SELECT id FROM ${ca_db_table_prefix:-0}_payment_methods WHERE creditcardcode = '$cardtype';")

#if we dont get a paymentmrthodid than just use 6389 as its teh default from TravelTek
if [ -z "${paymentmethodid}" ];then
  paymentmethodid=6389
fi

# chargetype=$(mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
# SELECT chargetype FROM ${ca_db_table_prefix}_payment_methods WHERE creditcardcode = '$cardtype';")

# chargevalue=$(mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
# SELECT chargevalue FROM ${ca_db_table_prefix}_payment_methods WHERE creditcardcode = '$cardtype';")

xml='xml=<?xml version="1.0"?>
  <request xmlns="http://fusionapi.traveltek.net/1.0/xsds">
    <auth username="'${ca_tt_username:-0}'" password="'${ca_tt_password:-0}'" />
    <method action="addreceipt" sitename="'${ca_tt_sitename:-0}'" bookingid="'${bookingid}'" paymentmethodid="'${paymentmethodid}'" creditvalue="'${amount}'" authcode="'${authcode}'" reference="'${reference}'" handlingfee="'${handlingfee}'" transactionref="'${transactionref}'" cardno="'${cardno}'" useportfoliobranch="1" />
  </request>'

file=$script_path/xml/addreceipt-$bookingid.xml

curl -o "${file}" -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/addreceipt" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "$xml"

#CREATE THE DOCUMENT
# Hard coded values need to be replaced with dynamic values if possible in the future via the config file, OR, use the sendemail - need confirmtation from TT if tradingnameid is required
xml='xml=<?xml version="1.0"?>
  <request xmlns="http://fusionapi.traveltek.net/1.0/xsds">
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" />
    <method action="createdocument" sitename="'${ca_tt_sitename}'" bookingid="'${bookingid}'" documentid="127265" tradingnameid="5708" >
      <attachments/>
    </method>
  </request>'

file=$script_path/xml/createdocument.xml

curl -o "${file}" -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/createdocument" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "$xml"