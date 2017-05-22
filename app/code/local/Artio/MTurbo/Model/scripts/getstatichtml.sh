#!/bin/sh

URL_LIST=$1
MIN_SIZE=$2

[ -f $URL_LIST ] || exit
[ -z "$MIN_SIZE" ] && MIN_SIZE=512

while read -r PLACE URL; do
  
  DIR=`echo $PLACE | sed 's/[^\/]*$//'`
  mkdir --mode=0775 -p $DIR

  wget -q --no-cookies --header "Cookie: artio_mturbo=true" --no-host-directories --html-extension -O $PLACE $URL

  if [ -f $PLACE ]; then
    SIZE=`du -b $PLACE | grep -Eo '^[0-9]+'`
    if [ $MIN_SIZE -gt $SIZE ]; then
      rm $PLACE
    fi   
  else
    echo "<!-- `date` -->" >> $PLACE
  fi

done < $URL_LIST