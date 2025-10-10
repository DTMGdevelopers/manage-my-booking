#!/bin/bash

script_path="$( cd "$( dirname "${BASH_SOURCE[0]}" )" >/dev/null 2>&1 && pwd )"
# shellcheck disable=SC1091
source "${script_path}/functions.sh"

file="$script_path/xml/getportfolio-82096361.xml"
bookingid=82096361
xmlstarlet ed --inplace -r "response/results/result/bookingdetails/elements/element/carhire" -v carhire_element "$file" 

# Create carhire table if not exists
mysql --login-path=local -N --local-infile --execute="\
USE ${ca_db_name:-0};\
CREATE TABLE IF NOT EXISTS ${ca_db_table_prefix:-0}_portfolio_carhire (\
	id INT PRIMARY KEY AUTO_INCREMENT,\
	bookingid INT,\
	bookingreference VARCHAR(64),\
	supplier VARCHAR(128),\
	supplierid INT,\
	suppliername VARCHAR(128),\
	pickupdate DATETIME,\
	dropoffdate DATETIME,\
	pickupdetail VARCHAR(255),\
	dropoffdetail VARCHAR(255),\
	vehicle VARCHAR(128),\
	unitsrequired INT,\
	sellprice DECIMAL(10,2),\
	nettprice DECIMAL(10,2),\
	grossprice DECIMAL(10,2),\
	vat DECIMAL(10,2),\
	status VARCHAR(64),\
	ownerid INT,\
	datebooked DATETIME,\
	deleted TINYINT(1) DEFAULT 0,\
	additionalinfo TEXT\
);\
"

# Import carhire
mysql --login-path=local -N --local-infile --execute="
USE ${ca_db_name:-0};
DELETE FROM ${ca_db_table_prefix:-0}_portfolio_carhire WHERE bookingid = ${bookingid};
LOAD XML LOCAL INFILE '$file' 
INTO TABLE ${ca_db_table_prefix:-0}_portfolio_carhire
ROWS IDENTIFIED BY '<carhire_element>'
	(bookingid, bookingreference, supplier, supplierid, suppliername, pickupdate, dropoffdate, pickupdetail, dropoffdetail, vehicle, unitsrequired, sellprice, nettprice, grossprice, vat, status, ownerid, datebooked, deleted, additionalinfo);\
"