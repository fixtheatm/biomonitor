# This python script reads the temperature from the sensor
# formats the result and outputs a json text file into the
# data folder
#
# IMPORTANT! The device id of 00002 is currently hard coded
#            The thermometer id is currently hard coded as well
#
import os
import glob
import datetime
import time

# to determine the id for a thermometer, do the following at a terminal window
#   sudo modprobe w1-gpio
#   sudo modprobe w1-therm
#
#   ls /sys/bus/w1/devices
#
# our deviceid is 28-000007080860

os.system('modprobe w1-gpio')
os.system('modprobe w1-therm')

json_folder='/usr/local/bin/BioReactor/'
json_date= time.strftime('%Y%m%d_%H%M%S')

json_file=json_folder + 'bio_temp_' + json_date + '.json'

deviceid='00002'

recorded_on = time.strftime('%Y-%m-%d %H:%M:%S')

lst=[]
lst.append('/sys/bus/w1/devices/28-000007080860/w1_slave')

def get_temp(device):
    f = open(device, 'r')
    data=f.readlines()
    f.close()
    deg_c=''
    if data[0].strip()[-3:] == 'YES':
        temp=data[1][data[1].find('t=')+2:]
        try:
            if float(temp)==0:
                deg_c=0
            else:
                deg_c = (float(temp)/1000)
        except:
            print ("Error with t=",temp)
            pass
    
    return deg_c;

for device in lst:
    device_name = device.split('/')[5]
    stemp = "%4.1f" % (get_temp(device))

    with open(json_file, 'w') as f:
        s='[{"deviceid":"' + deviceid + '","temperature":"' + stemp + '","recorded_on":' + \
            '"' + recorded_on + '"}]'
        f.write(s)
        f.close()
    print (s)
    
