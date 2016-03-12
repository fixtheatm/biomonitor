#!/usr/bin/env bash
#
# This is a test script to send the JSON datafiles from the
#  BioReactor system to the server. It scans the data folder
#  and sends each json file to the server via the httpie
#  package. The return code is checked to make sure that the
#  server has accepted the data. If the server responds with
#  200 then the file is moved to the sent folder.
#
# It detects whether we are on the Windows development machine
#  or whether we are on the Raspberry Pi Linux machine
#

# see which platform we are on. Windows is for testing.
# Linux is raspberry pi production
OS=$OSTYPE
Linux=false
if [[ ${OS:0:5} = "Linux" || ${OS:0:5} = "linux" ]] 
then
	Linux=true
fi

# name of the server we are posting to.
# 
SERVER="bioreactor.dev"

# root folder where are json files are being stored
# on Windows we are using "./" On the raspberry pi it will be "/usr/local/bin/bioreactor/"
# It needs to end in a forward slash
ROOT_FOLDER="./"
if ( $Linux )
then
	ROOT_FOLDER="/usr/local/bin/BioReactor/"
fi

# the wildcard strings denoting our json files
JSON_TEMP_WILDCARD="bio_temp*.json"
JSON_GAS_WILDCARD="bio_gas*.json"
JSON_LIGHT_WILDCARD="bio_light*.json"

# the folder where we are moving the json files that have been 
# successfully posted to the server
# for Windows we are using "./sent/" On the raspberry pi we are using "/usr/local/bin/bioreactor/sent/"
# It needs to end in a forward slash
SENT_FOLDER=$ROOT_FOLDER"sent/"

#log file name
LOG_FILE="httpsend.log"

# the logfile will reside in the root folder
LOG_FILE=$ROOT_FOLDER$LOG_FILE

# Move the datafile to the sent folder
#
# @parameter string filename to move
# @parameter string logdate
#
function moveJsonToSentFolder() {

	local fname=$1
    local logdate=$2

	local filename=$(basename "$fname")
    local extension="${filename##*.}"
    local fileroot="${filename%.*}"

	# tack on the datetime to the sent filename
	local newname="$SENT_FOLDER$fileroot"_"$logdate.$extension"

	#echo $newname
	#echo "filename[$filename] extension [$extension] fileroot [$fileroot]"

	echo "$logdate Moving [$fname] to Sent Folder as [$newname]" >> $LOG_FILE
	mv $fname $newname 2>> $LOG_FILE
}


# Send the datafile to the server
#
# @parameter string filename to send (it is full path)
#
function sendJsonToServer() {

	local fname=$1
	local route=$2
    	local logdate=$(date +"%Y_%m_%d_%H_%M_%S")

	echo "$logdate Posting ["$fname"] to Server with route ["$route"]" >> $LOG_FILE

	if http --check-status --timeout=2.5 POST http://$SERVER/$route < $fname &> /dev/null; then
		moveJsonToSentFolder "$fname" "$logdate"
	else
		case $? in
			2) echo "$logdate Request timed out!" >> $LOG_FILE ;;
			3) echo "$logdate Unexpected HTTP 3xx Redirection!" >> $LOG_FILE ;;
			4) echo "$logdate HTTP 4xx Client Error!" >> $LOG_FILE ;;
			5) echo "$logdate HTTP 5xx Server Error!" >> $LOG_FILE ;;
			6) echo "$logdate Exceeded --max-redirects=<n> redirects!" >> $LOG_FILE ;;
			*) echo "$logdate Other Error!" >> $LOG_FILE ;;
		esac
	fi
}

#echo "["$OSTYPE"]["$Linux"]"
#echo "["$ROOT_FOLDER$JSON_TEMP_WILDCARD"]"
#exit 0

# this line deals with the case where there are no files
shopt -s nullglob

# cycle through all the json bioreactor files in the folder
for f in $ROOT_FOLDER$JSON_TEMP_WILDCARD
do
	sendJsonToServer $f pitemp
done
for f in $ROOT_FOLDER$JSON_GAS_WILDCARD
do
	sendJsonToServer $f pigasflow
done
for f in $ROOT_FOLDER$JSON_LIGHT_WILDCARD
do
	sendJsonToServer $f pilight
done

exit 0

