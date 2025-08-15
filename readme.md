# Transparent Pixel Bundle

**Package:** `djdmg/transparent-pixel-bundle`  
A tiny Symfony bundle that adds a **1×1 transparent tracking pixel** for pages and emails. Each hit is stored with rich request details (IP, User-Agent, OS, browser, device, mobile/bot flags, headers, query params, cookies, referer, method, timestamp).

## Requirements
- PHP **8.2+**
- Symfony **7.x** (`framework-bundle`, `twig-bundle`)
- Doctrine ORM **3.x** + DoctrineBundle

## Install

~~~bash
composer require djdmg/transparent-pixel-bundle
~~~

If you allow contrib recipes (recommended):

~~~bash
composer config extra.symfony.allow-contrib true
~~~

If Flex recipe isn’t available, wire manually:

~~~php
<?php // config/bundles.php
return [
    Djdmg\TransparentPixelBundle\TransparentPixelBundle::class => ['all' => true],
];
~~~

~~~yaml
# config/routes/transparent_pixel.yaml
transparent_pixel_bundle:
  resource: '@TransparentPixelBundle/Controller/'
  type: attribute
~~~

## Database

Generate and run migrations **in your app**:

~~~bash
php bin/console doctrine:migrations:diff
php bin/console doctrine:migrations:migrate -n
~~~

Tables created: `tp_pixel`, `tp_pixel_hit`.

## Usage

### 1) Create or fetch a Pixel

~~~php
use Djdmg\TransparentPixelBundle\Service\PixelManager;

public function show(PixelManager $pixels)
{
    // Creates on first call, returns existing afterwards
    $pixel = $pixels->ensurePixel('newsletter-aug-2025', ['campaign' => 'summer25']);

    return $this->render('page.html.twig', ['pixel' => $pixel]);
}
~~~

### 2) Render in Twig (page)

~~~twig
{# Adds ?uid=… to the URL and logs it under "query" JSON #}
{{ transparent_pixel_tag(pixel, { uid: app.user ? app.user.id : 'guest' }) }}
~~~

Or just the URL:

~~~twig
<img src="{{ transparent_pixel_url(pixel, { source: 'landing' }) }}" width="1" height="1" style="display:none" alt="" loading="eager">
~~~

### 3) Use in emails (Symfony Mailer)

~~~php
use Symfony\Bridge\Twig\Mime\TemplatedEmail;

$pixel = $pixels->ensurePixel('reset-password');

$email = (new TemplatedEmail())
    ->to($user->getEmail())
    ->subject('Reset your password')
    ->htmlTemplate('emails/reset.html.twig')
    ->context([
        'pixel' => $pixel,
        'uid'   => (string)($user->getId() ?? 'guest'),
    ]);

$this->mailer->send($email);
~~~

~~~twig
{# templates/emails/reset.html.twig #}
{{ transparent_pixel_tag(pixel, { uid: uid }) }}
~~~

> **Tip (CLI/cron/workers):** set a base URL if you generate links outside HTTP requests:
~~~yaml
# config/packages/framework.yaml
framework:
  router:
    default_uri: 'https://app.example.com'
~~~

### 4) Read access details (code)

~~~php
// All hits (limit 500) or only for a specific pixel
$all      = $pixels->getAccessDetails(null, 500);
$perPixel = $pixels->getAccessDetails($pixel, 200);
~~~

Each item includes:
~~~php
[
  'at' => '2025-08-15T10:00:23+00:00',
  'ip' => '203.0.113.10',
  'os' => 'iOS 17',
  'browser' => 'Mobile Safari 17.4',
  'device' => 'iPhone',
  'isMobile' => true,
  'isBot' => false,
  'method' => 'GET',
  'referer' => 'https://example.com/page',
  'headers' => [...],
  'query' => ['uid' => '123'],
  'cookies' => [...],
  'ua' => 'Mozilla/5.0 (...)',
  'token' => 'ab12…',
  'pixel' => ['id' => 1, 'name' => 'newsletter-aug-2025'],
]
~~~

## Production notes

- **No-cache** headers ensure every view triggers a hit.
- **Trusted proxies** for real client IPs:
~~~yaml
framework:
  trusted_proxies: '%env(TRUSTED_PROXIES)%'
  trusted_headers: ['x-forwarded-for','x-forwarded-proto','x-forwarded-host','x-forwarded-port']
~~~
~~~env
# .env
TRUSTED_PROXIES=127.0.0.1,REMOTE_ADDR
~~~
- **Privacy/GDPR**: inform users, consider anonymizing IPs (e.g., truncate /24) and add a retention policy.

## Troubleshooting

- **Routes not found** → ensure routes are imported with `@TransparentPixelBundle/Controller/`.
- **Relative URLs in emails** → set `framework.router.default_uri`.
- **No tables** → run migrations (`diff` + `migrate`).

## License

MIT © djdmg