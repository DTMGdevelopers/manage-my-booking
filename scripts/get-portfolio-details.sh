#!/bin/bash
script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
sed -i -e 's/\r$//' $script_path/config.sh
source $script_path/config.sh
source $script_path/functions.sh

bookingid=$1

# $script_path/control-tables.sh $ca_db_table_prefix_portfolio_transfer
# $script_path/control-tables.sh $ca_db_table_prefix_portfolio_cruise 
# $script_path/control-tables.sh $ca_db_table_prefix_portfolio_flight 
# $script_path/control-tables.sh $ca_db_table_prefix_portfolio_accom
# $script_path/control-tables.sh $ca_db_table_prefix_portfolio_passanger

echo "bookingid = ${bookingid}"

file=$script_path/xml/getportfolio-${bookingid}.xml

xml='xml=<?xml version="1.0"?>
<request xmlns="http://fusionapi.traveltek.net/1.0/xsds"> 
    <auth username="'${ca_tt_username}'" password="'${ca_tt_password}'" /> 
    <method action="getportfolio" sitename="ignite.site.traveltek.net" bookingid="'${bookingid}'" externalreference="" />
</request>'

curl -o $file -X POST --url "https://fusionapi.traveltek.net/1.0/backoffice.pl/getportfolio" \
		-H "Content-Type: application/x-www-form-urlencoded" \
		-d "$xml" 

#REMOVE THE NAMESPACE FROM THE XML OR XMLSTARLET CAN NOT PROCESS IT
sed -i 's/xmlns="http:\/\/fusionapi.traveltek.net\/1.0\/xsds"/ /g' $file

####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### 
                                                                                                        
                                                                                                        
                                                                                                        
######## ########     ###    ########  #### ##    ##  ######      ##    ##    ###    ##     ## ######## 
   ##    ##     ##   ## ##   ##     ##  ##  ###   ## ##    ##     ###   ##   ## ##   ###   ### ##       
   ##    ##     ##  ##   ##  ##     ##  ##  ####  ## ##           ####  ##  ##   ##  #### #### ##       
   ##    ########  ##     ## ##     ##  ##  ## ## ## ##   ####    ## ## ## ##     ## ## ### ## ######   
   ##    ##   ##   ######### ##     ##  ##  ##  #### ##    ##     ##  #### ######### ##     ## ##       
   ##    ##    ##  ##     ## ##     ##  ##  ##   ### ##    ##     ##   ### ##     ## ##     ## ##       
   ##    ##     ## ##     ## ########  #### ##    ##  ######      ##    ## ##     ## ##     ## ######## 
                                                                                                        
                                                                                                        
                                                                                                        
####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### ####### 
tradingname=$(xmlstarlet sel -t -m "response/results/result/bookingdetails/@tradingname" -v . -n  $file )

if [ "${tradingname}" = "Explorations by Norwegian - Trade Partners" ] || [ "${tradingname}" = "My Cruises River Collection - Trade Partners" ] || [ "${tradingname}" = "MyCruises - Trade Partners" ] || [ "${tradingname}" = "My Cruises Touring Collection - Trade Partners" ] || [ "${tradingname}" = "MyCruises Trade" ] || [ "${tradingname}" = "My Holiday Touring Trade" ] || [ "${tradingname}" = "MyCruises River Collection - Trade Partners" ] || [ "${tradingname}" = "My Cruises Exclusive Luxury Collection - Trade Partners" ] || [ "${tradingname}" = "Flight Centre Exclusives - Trade Partners" ] ;then
echo "This booking is a trade account"
mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
DELETE FROM ${ca_db_table_prefix}_portfolios WHERE id = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_details WHERE id = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_transfer WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_cruise WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_cabins WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_flight WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_segments WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_attraction WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_accom WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_passenger WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_ticket WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_insurance WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_document WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_attachment WHERE bookingid = ${bookingid};
DELETE FROM ${ca_db_table_prefix}_portfolio_session WHERE bookingid = ${bookingid};"
exit
fi


xmlstarlet ed --inplace -r "response/results/result/bookingdetails/passengers" -v passenger_details $file
xmlstarlet ed --inplace -r "response/results/result/bookingdetails/@middlename" -v booking_details_middlename $file

xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/transfers" -v transfers_element $file
xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/cruise" -v cruise_element $file
#xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/cruise_element/@status" -v cabins_status ${file}
xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/flight" -v flight_element $file
xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/attraction" -v attraction_element $file
xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/accom" -v accom_element $file 
xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/ticket" -v ticket_element $file 
xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/insurance" -v insurance_element $file 





mysql --login-path=local --skip-column-names --local-infile --execute="USE "$ca_db_name";
DELETE FROM ${ca_db_table_prefix}_portfolio_details WHERE id = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
INTO TABLE ${ca_db_table_prefix}_portfolio_details 
ROWS IDENTIFIED BY '<bookingdetails>'
	(@id,
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
	/*middlename,*/
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
	tradingname,
	tradedepositamount,
	tradestopcommission,
	truetotalpaid,
	type,
	usedothercredentials,
	vat)
	SET 
	id = ${bookingid};

DELETE FROM ${ca_db_table_prefix}_portfolio_transfer WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix}_portfolio_transfer
ROWS IDENTIFIED BY '<transfers_element>'
	(accommodation,
	additionalinfo,
	adults,
	arrivalflighttime,
	atol,
	bookingformreceived,
	bookingid,
	bookingreference,
	children,
	commission,
	commissionable,
	commissionrate,
	confirmationreceived,
	costingid,
	datebooked,
	deleted,
	departureflighttime,
	depdate,
	displayorder,
	documentname,
	dropoffdate,
	dropoffdetail,
	dropofftype,
	elementbookedby,
	engine,
	faretype,
	grossprice,
	id,
	infants,
	localid,
	maxpassengers,
	nettprice,
	originalstatus,
	ownerid,
	paymentdatetype,
	paymentduedays,
	paymentduetype,
	penaltiesdate,
	pickupdate,
	pickupdetail,
	pickupresort,
	pickuptype,
	returndate,
	sellprice,
	shipname,
	status,
	supplier,
	supplierid,
	suppliername,
	termsandconditionsurl,
	ticketsreceived,
	type,
	unitsrequired,
	vat,
	vatrequirement,
	vehicle,
	voucherurl);

DELETE FROM ${ca_db_table_prefix}_portfolio_cruise WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix}_portfolio_cruise
ROWS IDENTIFIED BY '<cruise_element>'
	(additionalinfo,
	airportcode,
	arrdate,
	arrives,
	atol,
	bookingformreceived,
	bookingid,
	bookingreference,
	codetocruiseid,
	commission,
	commissionable,
	commissionrate,
	confirmationreceived,
	costingid,
	cruisefaretype,
	cruisename,
	cruisenumber,
	datebooked,
	deleted,
	departs,
	depdate,
	depositduedate,
	diningseating,
	diningsmoking,
	displayorder,
	documentname,
	duration,
	elementbookedby,
	enddate,
	engine,
	finalpaymentdate,
	grossprice,
	homeported,
	id,
	lineid,
	linename,
	localid,
	lockliveamends,
	nettprice,
	obstructedview,
	onboardcredit,
	originalstatus,
	ownerid,
	paymentdatetype,
	paymentduedays,
	paymentduetype,
	penaltiesdate,
	region,
	returndate,
	sellprice,
	shipid,
	shipname,
	shiprating,
	startdate,
	status,
	supplier,
	supplierid,
	suppliername,
	tablesize,
	termsandconditionsurl,
	ticketsreceived,
	type,
	vat,
	vatrequirement,
	voucherurl);



DELETE FROM ${ca_db_table_prefix}_portfolio_cabins WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
INTO TABLE ${ca_db_table_prefix}_portfolio_cabins
ROWS IDENTIFIED BY '<cabins>'
	(accessibilitycabin,
	bedconfig,
	bookingid,
	id,
	bedtype,
	cabindescription,
	cabingrade,
	cabinid,
	cabinname,
	cabintype,
	codetocruiseid,
	deckname,
	extrainfo,
	faretype,
	number,
	obstructedview,
	passengers,
	positiononship,
	sideofship,
	status);

DELETE FROM ${ca_db_table_prefix}_portfolio_cabins WHERE status = 'hidden' AND bookingid = ${bookingid};


DELETE FROM ${ca_db_table_prefix}_portfolio_flight WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
INTO TABLE ${ca_db_table_prefix}_portfolio_flight
ROWS IDENTIFIED BY '<flight_element>'
	(atol,
	bookingformreceived,
	bookingid,
	bookingreference,
	carriers,
	commission,
	commissionable,
	commissionrate,
	confirmationreceived,
	costingid,
	datebooked,
	deleted,
	depair,
	depdate,
	destair,
	destname,
	displayorder,
	documentname,
	duration,
	elementbookedby,
	engine,
	flightbaggageallowance,
	grossprice,
	hasbaggage,
	id,
	immediateticket,
	inarrivecode,
	inarrivedate,
	indepartcode,
	indepartdate,
	indepartname,
	localid,
	nettprice,
	oneway,
	originalstatus,
	outarrivecode,
	outarrivedate,
	outarrivename,
	outdepartcode,
	outdepartdate,
	ownerid,
	paymentdatetype,
	paymentduedays,
	paymentduetype,
	penaltiesdate,
	region,
	returndate,
	status,
	supplier,
	supplierid,
	suppliername,
	termsandconditionsurl,
	ticketoption,
	ticketsreceived,
	tickettimelimit,
	totalextras,
	type,
	ukonly,
	vat,
	vatrequirement,
	vendorreference,
	voucherurl);

DELETE FROM ${ca_db_table_prefix}_portfolio_flight WHERE status IN ('hidden','cancelled');

DELETE FROM ${ca_db_table_prefix}_portfolio_segments WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
INTO TABLE ${ca_db_table_prefix}_portfolio_segments
ROWS IDENTIFIED BY '<itineraries>'
(arrivaldate,
arrivaltime,
arrivedate,
arrterminal,
baggage,
bookingclass,
@bookingid,
carrier,
class,
depair,
depaircode,
sortorder,
departuredate,
departuretime,
depdate,
depname,
depterminal,
destair,
destaircode,
destname,
elementguid,
farebasis,
flightinternalref,
flightnumber,
id,
journey,
localid,
mealincluded,
opcarrier,
rawarrivaltime,
rawdeparturetime,
transactionguid,
deleted,
lastupdated,
stopairports,
status)
SET 
bookingid = ${bookingid};

DELETE FROM ${ca_db_table_prefix}_portfolio_segments WHERE status IN ('hidden','cancelled');

DELETE FROM ${ca_db_table_prefix}_portfolio_attraction WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix}_portfolio_attraction
ROWS IDENTIFIED BY '<attraction_element>'
(
	additionalinfo,
	airportcode,
	atol,
	bookingformreceived,
	bookingid,
	bookingreference,
	commission,
	commissionable,
	commissionrate,
	confirmationreceived,
	costingid,
	datebooked,
	deleted,
	depdate,
	description,
	destinationcountries,
	displayorder,
	documentname,
	duration,
	elementbookedby,
	enddate,
	engine,
	finalpaymentdate,
	grossprice,
	id,
	localid,
	location,
	name,
	nettprice,
	originalstatus,
	ownerid,
	paymentdatetype,
	paymentduedays,
	paymentduetype,
	penaltiesdate,
	sellprice,
	startdate,
	status,
	supplier,
	supplierid,
	suppliername,
	termsandconditionsurl,
	ticketsreceived,
	type,
	vat,
	vatrequirement,
	voucherurl
);

DELETE FROM ${ca_db_table_prefix}_portfolio_accom WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix}_portfolio_accom
ROWS IDENTIFIED BY '<accom_element>'
(
	additionalinfo,
	address1,
	address2,
	address3,
	address4,
	atol,
	boardbasis,
	bookingformreceived,
	bookingid,
	bookingreference,
	cancellationpolicy,
	checkin,
	checkout,
	commission,
	commissionable,
	commissionrate,
	confirmationreceived,
	contactname,
	contactphone,
	costingid,
	datebooked,
	deleted,
	depdate,
	description,
	destcode,
	destcountry,
	displayorder,
	documentname,
	elementbookedby,
	engine,
	essentialinfo,
	grossprice,
	handlingaddress,
	handlingagent,
	hotelagentref,
	hotelname,
	id,
	keycollection,
	lastupdated,
	localid,
	nettprice,
	numbernights, 
	originalstatus,
	ownerid,
	paymentdatetype,
	paymentduedays,
	paymentduetype,
	penaltiesdate,
	propertyid,
	propertyreference,
	propertytype,
	rating,
	resort,
	returndate,
	sellprice,
	status,
	supplier,
	supplierid,
	suppliername,
	termsandconditionsurl,
	ticketsreceived,
	type,
	vat,
	vatrequirement,
	voucherurl
);

DELETE FROM ${ca_db_table_prefix}_portfolio_passenger WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix}_portfolio_passenger
ROWS IDENTIFIED BY '<passenger_details>'
(
	address1,
	address2,
	address3,
	address4,
	age,
	altemail,
	altphone,
	bookingid,
	country,
	countryisocode,
	created,
	deleted,
	deletedby,
	deletedon,
	dob,
	email,
	emergencyemail,
	emergencyname,
	emergencyphone,
	faxnumber,
	firstname,
	gender,
	holidaymakerid,
	id,
	insuranceassistancecompany,
	insurancecompany,
	insurancepolicynumber,
	insurancetelnumber,
	lastname,
	lastupdated,
	lastupdatedby,
	leadpassenger,
	localid,
	loyaltynumber,
	mealoption,
	middlename,
	mobile,
	nationality,
	nationalityisocode,
	nokaddress1,
	nokaddress2,
	nokaddress3,
	nokaddress4,
	nokname,
	nokphone,
	nokrelationship,
	ownerid,
	passdocumenttype,
	passport,
	passportauthority,
	passportexpirydate,
	passportissuecountry,
	passportissuecountryisocode,
	passportissuedate,
	passportplaceofissue,
	paxguid,
	placeofbirth,
	postcode,
	redress,
	specialservices,
	telephone,
	title,
	titleid,
	type
);

DELETE FROM ${ca_db_table_prefix}_portfolio_ticket WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix}_portfolio_ticket
ROWS IDENTIFIED BY '<ticket_element>'
(
	additionalinfo,
	atol, 
	bookingformreceived,
	bookingid,
	bookingreference,
	commission,
	commissionable,
	commissionrate,
	costingid,
	country,
	date,
	datebooked,
	deleted,
	depdate,
	documentname,
	duration,
	elementbookedby,
	engine, 
	freetext,
	grossprice,
	id,
	localid,
	nettprice,
	originalstatus,
	ownerid, 
	paymentdatetype,
	paymentduedays,
	paymentduetype,
	penaltiesdate,
	sellprice,
	status,
	supplier, 
	supplierid, 
	suppliername, 
	termsandconditionsurl,
	ticketdescription,
	ticketsreceived,
	ticketssold,
	type,
	typeid,
	vat,
	vatrequirement,
	voucherurl,
	confirmationreceived
);

DELETE FROM ${ca_db_table_prefix}_portfolio_insurance WHERE bookingid = ${bookingid};

LOAD XML LOCAL INFILE '$file' 
REPLACE
INTO TABLE ${ca_db_table_prefix}_portfolio_insurance
ROWS IDENTIFIED BY '<insurance_element>'
(
	additionalinfo,
	adults,
	atol,
	bookingformreceived,
	bookingid ,
	bookingreference,
	commission,
	commissionable,
	commissionrate,
	confirmationreceived,
	costingid,
	deleted,
	depdate,
	documentname,
	elementbookedby,
	familypolicy,
	freetext,
	grossprice,
	id,
	localid,
	infants,
	nettprice,
	ownerid,
	policyenddate,
	policystartdate,
	paymentdatetype,
	paymentduedays,
	paymentduetype,
	penaltiesdate,
	supplierid,
	suppliername,
	termsandconditionsurl,
	ticketsreceived,
	traveldate,
	type,
	vat,
	vatrequirement,
	voucherurl
);"




