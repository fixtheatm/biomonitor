#!/usr/bin/env bash
#
# This is a test script to generate data for the BioMonitor Program.
#  It calls the makejsondata.sh script file 3 times, once for each
#  of the three data types. 
#
#  Put this script into the Windows Task Scheduler or into CRON on
#  Linux for the Raspberry Pi. Set it to run every 5 minutes.
#
#  This is not a production script as there is no error handling.
# it's purpos is to simulate the constant creation of json datafiles
# on the Raspberry Pi

logdate=$(date +"%Y%m%d_%H%M%S")

temp_fname="/usr/local/bin/BioReactor/bio_temp_"$logdate".json"
gas_fname="/usr/local/bin/BioReactor/bio_gas_"$logdate".json"
light_fname="/usr/local/bin/BioReactor/bio_light_"$logdate".json"

#echo $temp_fname
#echo $gas_fname
#echo $light_fname

#exit 0

bash /usr/local/bin/BioReactor/makejsondata.sh t > $temp_fname
bash /usr/local/bin/BioReactor/makejsondata.sh g > $gas_fname
bash /usr/local/bin/BioReactor/makejsondata.sh l > $light_fname


