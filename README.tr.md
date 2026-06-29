# mca/address

**Türkçe** | [English](README.md)

Laravel için ülke, şehir, ilçe ve mahalle: normalize tablolar, yönetim CRUD, cascading JSON API.

## Özellikler

- **4 seviye** — ülke → şehir → ilçe → mahalle
- **Yönetim CRUD** — `/mca/address` sekme navigasyonu
- **Cascading API** — `mca/settings` iletişim select'leri ve formlar
- **UAVT hazır** — `uavt_code` kolonları + ayarlar sayfası (sürücü v1.1)
- **Türkiye seed** — 81 il + ~970 ilçe + gömülü mahalle JSON (posta kodu dahil)
- **Hub entegrasyonu** — `/mca` panelinde Adres kartı

## Kurulum

```bash
composer require mca/address
php artisan mca:address:install
```

## API

```
GET /mca/address/api/cities?country_id=
GET /mca/address/api/districts?city_id=
GET /mca/address/api/neighborhoods?district_id=
```

## Tam Türkiye verisi

Mahalleler paket içindeki `database/data/turkey-neighbourhoods.json` dosyasından yüklenir (posta kodu dahil, harici paket gerekmez):

```bash
php artisan mca:address:import-turkey --fresh
```

Önce il/ilçe seed’inin yüklü olması gerekir (`mca:address:install`).

## Lisans

[MIT](LICENSE)
