#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
sed -i -e 's/\r$//' $script_path/config.sh
source $script_path/config.sh
source $script_path/functions.sh

bookingid=${1}

file=$script_path/xml/book-$bookingid.xml
cardtype=$(xmlstarlet sel -t -m "/transaction/cardtype" -v . -n $file )
amount=$(xmlstarlet sel -t -m "/transaction/amount" -v . -n $file )
authcode=$(xmlstarlet sel -t -m "/transaction/authcode" -v . -n $file )
reference=$(xmlstarlet sel -t -m "/transaction/reference" -v . -n $file )
handlingfee=$(xmlstarlet sel -t -m "/transaction/handlingfee" -v . -n $file )
transactionref=$(xmlstarlet sel -t -m "/transaction/transactionref" -v . -n $file )
cardno=$(xmlstarlet sel -t -m "/transaction/cardno" -v . -n $file )

paymentmethodid=$(mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
SELECT id FROM ${ca_db_table_prefix}_payment_methods WHERE creditcardcode = '$cardtype';")

#if we dont get a paymentmrthodid than just use 6389 as its teh default from TravelTek
if [ -z "${paymentmethodid}" ];then
  paymentmethodid=6389
fi

chargetype=$(mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
SELECT chargetype FROM ${ca_db_table_prefix}_payment_methods WHERE creditcardcode = '$cardtype';")

chargevalue=$(mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
SELECT chargevalue FROM ${ca_db_table_prefix}_payment_methods WHERE creditcardcode = '$cardtype';")

xml='xml=<?xml version="1.0"?>
  <request xmlns="http://fusionapi.traveltek.net/1.0/xsds">
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" /> 
    <method action="addreceipt" sitename="ignite.site.traveltek.net" bookingid="'${bookingid}'" paymentmethodid="'${paymentmethodid}'" creditvalue="'${amount}'" authcode="'${authcode}'" reference="'${reference}'" handlingfee="'${handlingfee}'" transactionref="'${transactionref}'" cardno="'${cardno}'" useportfoliobranch="1" />
  </request>'


file=$script_path/xml/addreceipt-$bookingid.xml

curl -o $file -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/addreceipt" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "$xml" 

#CREATE THE DOCUMENT 
xml='xml=<?xml version="1.0"?>
  <request xmlns="http://fusionapi.traveltek.net/1.0/xsds">
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" /> 
    <method action="createdocument" sitename="ignite.site.traveltek.net" bookingid="'${bookingid}'" documentid ="70894">
      <attachments/>
    </method>
  </request>'

file=$script_path/xml/createdocument.xml

curl -o $file -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/createdocument" \
    -H "Content-Type: application/x-www-form-urlencoded" \
    -d "$xml" 