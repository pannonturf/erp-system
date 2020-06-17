#!/bin/bash

DB_USER="keszgyep_db1"
DB_PASSWORD=")8mzao6W4}qK"
MAIL_NOTIFICATION="b.hegyi@turfgrass.hu"
BACKUP_FOLDER="/home/keszgyep/program/backup"
NUM_COPIES=7

for db in $(mysql -u${DB_USER} -p${DB_PASSWORD} -e 'show databases;' | grep -Ev "^(Database|mysql|information_schema)$")
do
    echo "processing ${db}"
    [ -d "${BACKUP_FOLDER}/${db}" ] || mkdir -p "${BACKUP_FOLDER}/${db}"
    cd "${BACKUP_FOLDER}/${db}"
    mysqldump --opt -u${DB_USER} -p${DB_PASSWORD} "${db}" | gzip > dump_$(date +%F_%T).sql.gz || mail -s "error backing up database ${db}" ${MAIL_NOTIFICATION}
    while [ `ls dump_* | wc -l` -gt ${NUM_COPIES} ]
    do
        FIRST=`ls -t dump_* | tail -n1`
        rm -f "${FIRST}"
    done
done

mkdir -p ${BACKUP_FOLDER}/ALL_DATABASES
cd ${BACKUP_FOLDER}/ALL_DATABASES
mysqldump --opt -u${DB_USER} -p${DB_PASSWORD} --events --ignore-table=mysql.event --all-databases | gzip > all_$(date +%F_%T).sql.gz || mail -s "error backing up all databases" ${MAIL_NOTIFICATION}
while [ `ls all_* | wc -l` -gt ${NUM_COPIES} ]
do
    FIRST=`ls -t all_* | tail -n1`
    rm -f "${FIRST}"
done