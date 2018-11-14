#!/bin/sh

case $1 in
    'start' )
        echo "Starting ...."
        nohup ./modc sms 20000000 >/dev/null 2>&1 &
        nohup ./modc order 20000000 >/dev/null 2>&1 &
        nohup ./modc oilStation 20000000 >/dev/null 2>&1 &
        nohup ./modc logisticsCompany 20000000 >/dev/null 2>&1 &
        echo "Start end .... "
        ;;

    'stop' )
        echo "Stopping .... "

        kill `ps -ef | egrep 'sms 20000000|order 20000000|oilStation 20000000|logisticsCompany 20000000'| grep -v egrep | awk '{print $2}'`

        echo "Stop end .... "
        ;;

    'restart'|'reload' )
        ${0} stop
        ${0} start
        ;;

    'list' )

        ps -ef | egrep 'sms 20000000|order 20000000|oilStation 20000000|logisticsCompany 20000000'| grep -v egrep

        ;;

    *)
echo "usage: `basename $0` {start|restart|stop|list}"
esac
