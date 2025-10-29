#!/bin/bash

script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"

# shellcheck disable=SC1091
source "${script_path}/functions.sh"

bookingid=$1

"${script_path}"/control-tables.sh "${ca_db_table_prefix:-0}_portfolio_document"
"${script_path}"/control-tables.sh "${ca_db_table_prefix:-0}_portfolio_attachment"

echo "bookingid = ${bookingid}"

file=${script_path}/xml/listportfoliodocuments-$bookingid.xml

xml='xml=<?xml version="1.0"?>
<request xmlns="http://fusionapi.traveltek.net/1.0/xsds"> 
    <auth username="'${ca_tt_username:-0}'" password="'${ca_tt_password:-0}'" /> 
    <method action="listportfoliodocuments" sitename="'${ca_tt_sitename:-0}'" bookingid="'${bookingid}'" externalreference="" />
</request>'

curl -o "${file}" -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/listportfoliodocuments" \
		-H "Content-Type: application/x-www-form-urlencoded" \
		-d "$xml" 

#REMOVE THE NAMESPACE FROM THE XML OR XMLSTARLET CAN PROCESS IT
sed -i 's/xmlns="http:\/\/fusionapi.traveltek.net\/1.0\/xsds"/ /g' "$file"

#MARK: GET AUTH TOKEN
token_file=$script_path/json/token.json
ca_tt_auth_b64="$(echo -n "${ca_tt_username}:${ca_tt_password}" | base64)"
mkdir -p "${script_path}/json"
curl -s -o "$token_file" -X POST --url "https://fusionapi.traveltek.net/2.0/json/token.pl" \
		-H  "Content-Type: application/x-www-form-urlencoded" \
		-H  "Authorization: Basic ${ca_tt_auth_b64}" \
		--data-urlencode "grant_type=client_credentials" \
		--data-urlencode "scope=docdownload"
token=$(jq -r '.access_token' "$token_file")

#MARK: IMPORT DATA INTO DATABASE
mysql --login-path=local --skip-column-names --local-infile --execute="USE ${ca_db_name:-0};
DELETE FROM ${ca_db_table_prefix:-0}_portfolio_document WHERE bookingid = $bookingid;

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix:-0}_portfolio_document
ROWS IDENTIFIED BY '<document>'
	(id,
	documentid,
	name,
	created,
	link,
	@bookingid)
SET 
	bookingid = $bookingid;

UPDATE ${ca_db_table_prefix:-0}_portfolio_document SET link = CONCAT(link,'&requestid=${token}') WHERE bookingid = $bookingid;

DELETE FROM ${ca_db_table_prefix:-0}_portfolio_attachment WHERE bookingid = $bookingid;

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix:-0}_portfolio_attachment
ROWS IDENTIFIED BY '<attachment>'
	(id,
	documentid,
	name,
	link,
	@bookingid)
SET 
	bookingid = $bookingid;

UPDATE ${ca_db_table_prefix:-0}_portfolio_attachment SET link = CONCAT(link,'&requestid=${token}') WHERE bookingid = $bookingid;"
