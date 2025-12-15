<p align="center">
  <a href="https://laravel.com" target="_blank">
    <img
      src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg"
      width="400"
      alt="Laravel Logo"
    >
  </a>
</p>

<p align="center">
  <a href="https://github.com/laravel/framework/actions">
    <img src="https://github.com/laravel/framework/workflows/tests/badge.svg" alt="Build Status">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version">
  </a>
  <a href="https://packagist.org/packages/laravel/framework">
    <img src="https://img.shields.io/packagist/l/laravel/framework" alt="License">
  </a>
</p>

<h1>🛒 Laravel Webshop</h1>

<p>
  Dit project is een moderne webshop gebouwd met <strong>Laravel</strong>,
  <strong>Tailwind CSS</strong> en <strong>Flowbite</strong>.<br>
  De focus ligt op een schaalbare codebase, nette theming en een snelle development-flow.
</p>

<hr>

<h2>🚀 Tech stack</h2>
<ul>
  <li><strong>Laravel</strong> (v12+)</li>
  <li><strong>PHP</strong> 8.2+</li>
  <li><strong>Tailwind CSS</strong></li>
  <li><strong>Flowbite</strong></li>
  <li><strong>Vite</strong></li>
  <li><strong>MySQL / MariaDB</strong></li>
  <li><strong>Git & GitHub</strong></li>
</ul>

<hr>

<h2>📁 Project structuur (belangrijk)</h2>

<pre><code>resources/
└── views/
    └── themes/
        └── default/
            ├── layouts/
            ├── components/
            └── pages/
</code></pre>

<ul>
  <li><code>layouts/</code> → basis layouts</li>
  <li><code>components/</code> → herbruikbare UI onderdelen</li>
  <li><code>pages/</code> → pagina’s zoals home, productoverzicht, etc.</li>
</ul>

<p>
  Deze structuur maakt het mogelijk om later <strong>meerdere thema’s / klanten</strong> te ondersteunen.
</p>

<hr>

<h2>🧑‍💻 Vereisten</h2>
<ul>
  <li>PHP 8.2+</li>
  <li>Composer</li>
  <li>Node.js (LTS aanbevolen)</li>
  <li>npm</li>
  <li>MySQL of MariaDB</li>
  <li>(Optioneel) Laravel Herd</li>
</ul>

<hr>

<h2>⚙️ Project lokaal opzetten</h2>

<h3>1️⃣ Repository clonen</h3>
<pre><code>git clone https://github.com/&lt;jouw-username&gt;/&lt;repo-naam&gt;.git
cd &lt;repo-naam&gt;
</code></pre>

<h3>2️⃣ PHP dependencies installeren</h3>
<pre><code>composer install</code></pre>

<h3>3️⃣ Node dependencies installeren</h3>
<pre><code>npm install</code></pre>

<h3>4️⃣ Environment configureren</h3>
<pre><code>copy .env.example .env</code></pre>

<pre><code>APP_NAME=Webshop
APP_ENV=local
APP_DEBUG=true
APP_URL=http://127.0.0.1:8000

DB_DATABASE=webshop
DB_USERNAME=root
DB_PASSWORD=
</code></pre>

<pre><code>php artisan key:generate</code></pre>

<h3>5️⃣ Database migraties draaien</h3>
<pre><code>php artisan migrate</code></pre>

<h3>6️⃣ Frontend starten (Tailwind / Vite)</h3>
<pre><code>npm run dev</code></pre>

<p>Laat dit commando open staan tijdens development.</p>

<h3>7️⃣ Applicatie starten</h3>

<p>Zonder Herd:</p>
<pre><code>php artisan serve --port=8000</code></pre>

<p>Open daarna:</p>
<pre><code>http://127.0.0.1:8000</code></pre>

<p>Met Laravel Herd:</p>
<pre><code>http://&lt;projectnaam&gt;.test</code></pre>

<hr>

<h2>🎨 Styling & UI</h2>
<ul>
  <li>Styling gebeurt met <strong>Tailwind CSS</strong></li>
  <li>UI componenten komen van <strong>Flowbite</strong></li>
</ul>

<pre><code>import 'flowbite';</code></pre>

<hr>

<h2>❌ Wat staat bewust NIET in Git</h2>
<ul>
  <li><code>.env</code></li>
  <li><code>vendor/</code></li>
  <li><code>node_modules/</code></li>
  <li>build artifacts</li>
</ul>

<p>Gebruik altijd <code>.env.example</code> als basis.</p>

<hr>

<h2>🛠️ Roadmap</h2>
<ul>
  <li>Product & categorie models</li>
  <li>Admin panel (Filament)</li>
  <li>Winkelmandje</li>
  <li>Checkout & betalingen</li>
  <li>Klant-specifieke theming</li>
</ul>

<hr>

<h2>📄 License</h2>
<p>
  Dit project is bedoeld voor privé / intern gebruik.<br>
  Gebruik of distributie alleen in overleg.
</p>

<hr>

<h2>🙌 Credits</h2>
<p>Gebouwd met ❤️ met Laravel & Tailwind.</p>
