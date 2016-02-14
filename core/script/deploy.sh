#!/bin/sh
_date=`date +"%Y%m%d%I%M%S"`
mv ~/web/snow69it_kindle ~/web/snow69it_kindle-$_date
cp -r ~/web/snow69it_kindle-dev ~/web/snow69it_kindle
chmod -R u+x ~/web/snow69it_kindle/*
