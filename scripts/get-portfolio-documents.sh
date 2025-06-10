#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
sed -i -e 's/\r$//' $script_path/config.sh
source $script_path/config.sh
source $script_path/functions.sh

bookingid=$1

$script_path/control-tables.sh $ca_db_table_prefix_portfolio_document
$script_path/control-tables.sh $ca_db_table_prefix_portfolio_attachment

echo "bookingid = $bookingid"

file=$script_path/xml/listportfoliodocuments-$bookingid.xml

xml='xml=<?xml version="1.0"?>
<request xmlns="http://fusionapi.traveltek.net/1.0/xsds"> 
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" /> 
    <method action="listportfoliodocuments" sitename="ignite.site.traveltek.net" bookingid="'${bookingid}'" externalreference="" />
</request>'

curl -o $file -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/listportfoliodocuments" \
		-H "Content-Type: application/x-www-form-urlencoded" \
		-d "$xml" 

#REMOVE THE NAMESPACE FROM THE XML OR XMLSTARLET CAN PROCESS IT
sed -i 's/xmlns="http:\/\/fusionapi.traveltek.net\/1.0\/xsds"/ /g' $file

mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
DELETE FROM "$ca_db_table_prefix"_portfolio_document WHERE bookingid = $bookingid;

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE "$ca_db_table_prefix"_portfolio_document
ROWS IDENTIFIED BY '<document>'
	(id,
	documentid,
	name,
	created,
	link,
	@bookingid)
SET 
	bookingid = $bookingid;

DELETE FROM "$ca_db_table_prefix"_portfolio_attachment WHERE bookingid = $bookingid;

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE "$ca_db_table_prefix"_portfolio_attachment
ROWS IDENTIFIED BY '<attachment>'
	(id,
	documentid,
	name,
	link,
	@bookingid)
SET 
	bookingid = $bookingid;"



