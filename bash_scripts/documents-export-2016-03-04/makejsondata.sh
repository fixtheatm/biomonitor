#!/usr/bin/env bash
#
# This is a test script to generate JSON data for the
# Laravel BioMonitor system. It can generate the 3 data
# record types. Temperature, Gasflow and LightReadings
#
# %1 the type of record 't' 'g' 'l'
# %2 is the deviceid. If not specified then uses '00002'
#
# This script assumes the existence of a master json text file
# in the same folder as this script:
#   master_datavalue.json    
#
# Sample format of master_datavalue.json file:
#
#   [{"deviceid": "_deviceid_","_valuename_":_value_,"recorded_on":"_recorded_on_"}]
#
# The _recorded_on_ date is set to the current date and time.
# The _valuename_ is set to temperature,lux or flow
# The _deviceid_ is set to the %2 parameter (or 00002 if none passed)
#
# The _value_ is set to a random value appropriate for each datatype:
#
#  temperatures: 18.0 through 35.0 or so
#  gasflow       1.0 through 10.0 or so
#  lightreading  200 through 10000 or so
#
#  Note: Gasflows need to be storeed as >= 1 because otherwise the client side 
#        javascript Chart library has a fit.
#        So just to make life easier, we will multiply the actual readings
#        to make them fit this and then note that on the chart scale.
#
#

JSONFILE="/usr/local/bin/BioReactor/master_datavalue.json"

# make sure the json template file is here
if [ ! -e $JSONFILE ]; then
 >&2 echo "Missing the "$JSONFILE" in this directory (see script comments)"
 exit 1
fi

# create the recorded_on value in our expected format
NOW=$(date +"%Y-%m-%d %H:%M:%S")
#echo $NOW

# ignore these lines of comments
# cd /dada &> /dev/null
# echo rv: $?
# cd $(pwd) &> /dev/null
# echo rv: $?

# make sure there is a data type parameter
if [ $# -eq 0 ]; then
 >&2 echo "Missing parameter for data type. Need t or g or l"
 exit 1
fi

# create the correct valuename and approprate semi-random value
case "$1" in
 t)
  VALUENAME="temperature"
  VALUE=$(( ( RANDOM % 18 )  + 17 )).$(( ( RANDOM % 9 ) ))
  ;;
 g)
  VALUENAME="flow"
  VALUE=$(( ( RANDOM % 9 )  + 1 )).$(( ( RANDOM % 9 ) ))
  ;;
 l)
  VALUENAME="lux"
  VALUE=$(( ( RANDOM % 10000 )  + 200 ))
  ;;
 *)
  >&2 echo "Bad parameter for data type. Need t or g or l"
  exit 1
  ;;
esac

# if a second parameter is specified, use it as the deviceid
# otherwise use "00002"
DEVICEID="${2}"
if [ "$DEVICEID" = "" ]; then
 DEVICEID="00002"
fi

# make the parameter string to be passed to sed, the command line string editor
SEDPARAMS="s/_value_/$VALUE/g;s/_deviceid_/$DEVICEID/g;s/_recorded_on_/$NOW/g;s/_valuename_/$VALUENAME/g"
#echo $SEDPARAMS

# call sed. Its output goes to the console,
# which you can then redirect to a file or directly into an http call
sed "$SEDPARAMS" $JSONFILE

exit 0        
