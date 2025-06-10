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
INTO TABLE "$ca_db_table_prefix"_portfolios 
ROWS IDENTIFIED BY '<portfolio>'
	(id,
	bookingreference,
	lastupdated);

SELECT id FROM "$ca_db_table_prefix"_portfolios WHERE TIMESTAMPDIFF(MINUTE,lastupdated, now()) <= 5;" | while read -r id

do 
echo "id = $id"

file=$script_path/xml/getportfolio-$id.xml
xml='xml=<?xml version="1.0"?>
<request xmlns="http://fusionapi.traveltek.net/1.0/xsds"> 
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" /> 
    <method action="getportfolio" sitename="ignite.site.traveltek.net" bookingid="'${id}'" externalreference="" />
</request>'

curl -o $file -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/getportfolio" \
		-H "Content-Type: application/x-www-form-urlencoded" \
		-d "$xml" 

mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
DELETE FROM "$ca_db_table_prefix"_portfolio_details WHERE id = $id;

LOAD XML LOCAL INFILE '$file' 
INTO TABLE "$ca_db_table_prefix"_portfolio_details 
ROWS IDENTIFIED BY '<bookingdetails>'
	(id,
	additionaltaxes,
	agencybranchname,
	agencyuserfullname,
	altemail,
	altphone,
	apportionedcredit,
	atol,
	backofficesystem,
	billingaddress1,
	billingaddress2,
	billingaddress3,
	billingaddress4,
	billingaddress5,
	billingcity,
	billingcountry,
	billingcountryisocode,
	billingcounty,
	billingpostcode,
	blocksite,
	bondingtype,
	bookingcurrency,
	bookingreference,
	bookingteam,
	branchcategory1id,
	branchcategory2id,
	branchcategory3id,
	branchid,
	cancellationpolicy,
	clientreference,
	commission,
	confirmationreceived,
	confirmationsent,
	consultantemail,
	consultantextension,
	consultantfirstname,
	consultantname,
	consultantshortname,
	created,
	createdby,
	createdbyuserid,
	cruisehomeported,
	currencyshift,
	currentduetoop,
	customduedate,
	customerid,
	customstatusid,
	datebooked,
	deleted,
	departurepoint,
	departurepointdate,
	departurepointname,
	depdate,
	depositduedate,
	destcountry,
	destcountryisocode,
	dob,
	duedate,
	duetoop,
	duetoopsupplieronlycostings,
	elementtotal,
	email,
	enquiryid,
	enquiryreferrer,
	exitdate,
	externalreference,
	faxnumber,
	feerefund,
	finalduedate,
	firsthotel,
	firsthotelrating,
	firstname,
	flightplus,
	flightplusoptout,
	grosscost,
	handoffguid,
	holidaycost,
	holidaycostpp,
	holidaymakerid,
	importantinfo,
	language,
	lastname,
	lastupdated,
	localid,
	loyaltypoints,
	middlename,
	mobilephone,
	nights,
	nonreceiptedtransactionfees,
	nonreceiptedtransactions,
	outstanding,
	outstandingcredit,
	outstandingtoop,
	ownerid,
	paidtoop,
	paxcount,
	paxinfants,
	paxnoinfant,
	penaltiesdate,
	profit,
	recordowner,
	reference,
	referrer,
	returndate,
	selfbonded,
	shippingaddress1,
	shippingaddress3,
	shippingaddress4,
	shippingcountry,
	shippingcountryisocode,
	shippingpostcode,
	sid,
	status,
	surprisebooking,
	teamid,
	telephone,
	ticketondemand,
	ticketsreceived,
	title,
	titleid,
	totalagencycommission,
	totalcharge,
	totalcommission,
	totalcost,
	totalcostpp,
	totalcredits,
	totaldeposit,
	totaldiscount,
	totaldiscountprice,
	totaldiscounts,
	totaldue,
	totalextramargin,
	totalfees,
	totaloutstanding,
	totaloverridecommission,
	totalprediscountprice,
	totalreceiptcredit,
	totalreceived,
	totalrefund,
	totalstandardcommission,
	totalsupplement,
	tradecommission,
	tradedepositamount,
	tradestopcommission,
	truetotalpaid,
	type,
	usedothercredentials,
	vat);"
done 