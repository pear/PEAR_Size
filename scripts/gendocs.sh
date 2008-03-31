#!/bin/sh
phpdoc -d .. -i  "docs,doc*,example*,generate_package.xml,package.xml,CVS" \
       -t ../docs/generated  -o HTML:frames:earthli -s -p -dn "PEAR_Size"  \
       -dc pear -ti "PEAR_Size"
