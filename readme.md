# BioReactors

This repository contains code to accept sensor data, store it in a database, and provide a website to allow access to the data.  Either as standard graphs, or as raw measurement data in Excel spreadsheets.

The was developed to support monitoring of the BioReactors for [Solar Biocells](http://www.solarbiocells.com/).  It should be relatively to modify it for other applications that have a way of sending regular measurement data over http.

For Solar Biocells, and [Fixing the Atmosphere](http://www.fixingtheatmosphere.com/), the data is collected by sensors attached to a Raspberry Pi board using python scrips and cron jobs.

The website was written using the Laravel framework, sqlite database, maatwebsite/excel package jquery, and Chart.js javascript libraries.
