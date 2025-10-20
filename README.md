git clone https://github.com/mdikifahriza/wifi1/
cp .env.example .env
composer install
php artisan key:generate
composer dumpautoload
setup isi config:
MIDTRANS_SERVER_KEY=
MIDTRANS_CLIENT_KEY=
MIDTRANS_MERCHANT_ID=
MIDTRANS_ISPRODUCTION=false
MIDTRANS_ISSANITIZED=true
MIDTRANS_IS3DS=true
DB_CONNECTION=mariadb
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=terserah
DB_USERNAME=root
DB_PASSWORD=
WINDOWS_USERNAME=USERNAMA_WINDOWS #username windows kenek didelok pas login
HOTSPOT_ON_BAT=C:\path laravel\wifi\scripts\hotspot_on.bat
HOTSPOT_OFF_BAT=C:\path laravel\www\wifi\scripts\hotspot_off.bat

buat db mysql terserah
php artisan migrate
php artisan db:seed
php artisan serve

rute admin
url/admin

rute callback
url/api/midtrans/submit/notif

admin
admin@wifi.id
password

biar fungsi bisa berjalan
chatgpt
cara instal adb driver dan minimal adb fastboot
cara konek hp ke adb

hotspot_on.bat dan hotspot_off.bat buat hape realme c2
rubah
chatgpt rubah perintah adb ini ke tipe ponsel
