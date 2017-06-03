#!/bin/sh



main () {
echo $1
case $1 in
    -s) deployMode="-stg";;
    -p) deployMode="";;
    *)  echo "-s ステージング　-p プロダクション"
        return
esac

_date=`date +"%Y%m%d%I%M%S"`

echo ~/web/snow69it_kindle-$_date
echo ~/web/snow69it_kindle$deployMode

mv ~/web/snow69it_kindle$deployMode ~/web/snow69it_kindle$deployMode-$_date
cp -r ~/web/snow69it_kindle-dev ~/web/snow69it_kindle$deployMode
chmod -R u+x ~/web/snow69it_kindle

}

main $@
