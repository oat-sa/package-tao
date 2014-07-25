#!/bin/sh

cp -a $1 tmp1.csv

sed "1d" tmp1.csv > tmp2.csv
sed "s/\"/\\\"/g" tmp2.csv > tmp1.csv
sed "s/\t/\";\"/g" tmp1.csv > tmp2.csv
sed "s/^/\"/g" tmp2.csv > tmp1.csv
sed "s/$/\"/g" tmp1.csv > tmp2.csv

cp -a tmp2.csv $2
rm -fr tmp1.csv tmp2.csv

