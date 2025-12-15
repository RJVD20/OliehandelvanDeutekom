<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel Logo"></a></p>

<p align="center">
<a href="https://github.com/laravel/framework/actions"><img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

# 🛒 Laravel Webshop

Dit project is een moderne webshop gebouwd met **Laravel**, **Tailwind CSS** en **Flowbite**.  
De focus ligt op een schaalbare codebase, nette theming en een snelle development-flow.

---

## 🚀 Tech stack

- **Laravel** (v12+)
- **PHP** 8.2+
- **Tailwind CSS**
- **Flowbite**
- **Vite**
- **MySQL / MariaDB**
- **Git & GitHub**

---

## 📁 Project structuur (belangrijk)

resources/
└── views/
└── themes/
└── default/
├── layouts/
├── components/
└── pages/

yaml
Code kopiëren

- `layouts/` → basis layouts  
- `components/` → herbruikbare UI onderdelen  
- `pages/` → pagina’s zoals home, productoverzicht, etc.

Deze structuur maakt het mogelijk om later **meerdere thema’s / klanten** te ondersteunen.

---

## 🧑‍💻 Vereisten

Zorg dat je dit lokaal hebt geïnstalleerd:

- PHP 8.2+
- Composer
- Node.js (LTS aanbevolen)
- npm
- MySQL of MariaDB
- (Optioneel) Laravel Herd

---

## ⚙️ Project lokaal opzetten

### 1️⃣ Repository clonen
```bash
git clone https://github.com/<jouw-username>/<repo-naam>.git
cd <repo-naam>
2️⃣ PHP dependencies installeren
bash
Code kopiëren
composer install
3️⃣ Node dependencies installeren
bash
Code kopiëren
npm install
4️⃣ Environment configureren
Maak een .env bestand aan op basis van het voorbeeld:

bash
Code kopiëren
copy .env.example .env
Pas in .env minimaal aan:

env
Code kopiëren
APP_NAME=Webshop
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_DATABASE=webshop
DB_USERNAME=root
DB_PASSWORD=
Genereer daarna de app key:

bash
Code kopiëren
php artisan key:generate
5️⃣ Database migraties draaien
bash
Code kopiëren
php artisan migrate
6️⃣ Frontend starten (Tailwind / Vite)
bash
Code kopiëren
npm run dev
Laat dit commando open staan tijdens development.

7️⃣ Applicatie starten
Zonder Herd:

bash
Code kopiëren
php artisan serve --port=8000
Open daarna:

cpp
Code kopiëren
http://127.0.0.1:8000
Met Laravel Herd:

arduino
Code kopiëren
http://<projectnaam>.test
🎨 Styling & UI
Styling gebeurt met Tailwind CSS

UI componenten komen van Flowbite

Flowbite JS is geladen via:

js
Code kopiëren
import 'flowbite';
❌ Wat staat bewust NIET in Git
.env

vendor/

node_modules/

build artifacts

Gebruik altijd .env.example als basis.

🛠️ Roadmap (globaal)
 Product & categorie models

 Admin panel (Filament)

 Winkelmandje

 Checkout & betalingen

 Klant-specifieke theming

📄 License
Dit project is bedoeld voor privé / intern gebruik.
Gebruik of distributie alleen in overleg.

🙌 Credits
Gebouwd met ❤️ met Laravel & Tailwind.

markdown
Code kopiëren

Als je wilt, kan ik ’m ook:
- **iets commerciëler** maken (voor klanten)
- uitbreiden met **production deploy (DirectAdmin)**
- of er een **CONTRIBUTING.md** naast zetten