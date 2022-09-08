#!/bin/bash
php artisan queue:work --queue=$1 --once > /dev/null 2>/dev/null &