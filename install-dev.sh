#!/bin/bash
cp .env.dev .env
chmod 777 .env
# change tmp folder permission
# chmod -R 777 app/tmp
# chmod +x app/Console/cake