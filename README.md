struktur folder
```
wifi1/
│
├── app/
│   ├── Http/
│   │   └── Controllers/
│   │       ├── AdminController.php
│   │       ├── Controller.php
│   │       ├── FetcHTTP.php
│   │       ├── HotspotController.php
│   │       ├── Ordercontroller.php
│   │       ├── Qrgenerator.php
│   │       ├── simulateSubmit.php
│   │       └── webhookendp.php
│   │
│   ├── Models/
│   │   ├── Order.php
│   │   └── User.php
│   │
│   ├── Providers/
│   │   └── AppServiceProvider.php
│   │
│   ├── Services/
│   │   └── Htmlhelper.php
│   │
│   └── View/
│       └── Components/
│           └── Qrcode.php
│
├── bootstrap/
│   └── cache/
│       ├── app.php
│       └── providers.php
│
├── config/
│   ├── app.php
│   ├── database.php
│   ├── filesystems.php
│   ├── logging.php
│   ├── midtransAPI.php
│   ├── queue.php
│   ├── sanctum.php
│   ├── services.php
│   └── session.php
│
├── database/
│   ├── factories/
│   │   └── UserFactory.php
│   │
│   ├── migrations/
│   │   ├── 0001_01_01_000000_create_users_table.php
│   │   └── 2025_10_19_090224_create_orders_table.php
│   │
│   └── seeders/
│       ├── AdminUserSeeder.php
│       └── DatabaseSeeder.php
│
├── public/
│   ├── js/ custom.js
│
├── resources/
│   ├── js/
│   │   ├── app.js
│   │   └── bootstrap.js
│   │
│   └── views/
│       ├── components/
│       │   ├── LOG.blade.php
│       │   ├── admin_login.blade.php
│       │   ├── admin_orders.blade.php
│       │   ├── countdown.blade.php
│       │   ├── displaytransaction.blade.php
│       │   ├── qrcode.blade.php
│
└── routes/
   ├── api.php
   ├── console.php
   └── web.php

```

```
git clone https://github.com/mdikifahriza/wifi1/
cp .env.example .env
composer install
php artisan key:generate
composer dumpautoload
```

setup isi .env:

```
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
```

buat db mysql terserah

```
php artisan migrate
php artisan db:seed
php artisan serve
```

rute admin

```
url/admin
```

rute callback

```
url/api/midtrans/submit/notif
```

admin

```
admin@wifi.id
password
```

biar fungsi bisa berjalan

```
chatgpt
cara instal adb driver dan minimal adb fastboot
cara konek hp ke adb
```

hotspot_on.bat dan hotspot_off.bat buat hape realme c2

```
rubah
chatgpt rubah perintah adb ini ke tipe ponsel
```
