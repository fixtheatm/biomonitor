#!/usr/bin/env bash
#
# This is a test script to generate data for the BioMonitor Program.
#  It calls the makejsondata.sh script file 3 times, once for each
#  of the three data types. The output of that script is piped
#  directly to the httpie program. That program simulates a browser
#  call, directly sending the json output to the web server.
#
#  Put this script into the Windows Task Scheduler or into CRON on
#  Linux for the Raspberry Pi. Set it to run every 5 minutes.
#
#  This is not a production script as there is no error handling.
#  In particular, the data will be lost if the server does not 
#  store it! A production version should store the data to a local file,
#  then use the file as input to the htppie call. If the return code
#  from the web call is not 200, log that and then try again later.
#  If the result is 200, the local copy of the file can be deleted.

sh makejsondata.sh t | http http://laravel.dev/pitemp

sh makejsondata.sh g | http http://laravel.dev/pigasflow

sh makejsondata.sh l | http http://laravel.dev/pilight