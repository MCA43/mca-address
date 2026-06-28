# mca/address

**English** | [Türkçe](README.tr.md)

Countries, cities, districts and neighborhoods for Laravel: normalized tables, admin CRUD, cascading JSON API.

## Features

- **4 levels** — country → city → district → neighborhood
- **Admin CRUD** — `/mca/address` with tab navigation
- **Cascading API** — for `mca/settings` contact selects and custom forms
- **UAVT ready** — nullable `uavt_code` columns + settings stub (driver in v1.1)
- **Turkey seed** — 81 provinces + ~970 districts (embedded data)
- **Hub integration** — Address card on `/mca` dashboard

## Install

```bash
composer require mca/address
php artisan mca:address:install
```

## API (mca/settings compatible)

```
GET /mca/address/api/cities?country_id=
GET /mca/address/api/districts?city_id=
GET /mca/address/api/neighborhoods?district_id=
```

Response: `{ "data": [{ "id": 1, "name": "Bursa" }] }`

## Full Turkey data

## Full Turkey data

Neighborhoods (with postal codes) are imported from [turkey-neighbourhoods](https://github.com/muratgozel/turkey-neighbourhoods):

```bash
php artisan mca:address:import-turkey --fresh
```

On first run the JSON is downloaded and cached at `storage/app/mca-address/neighbourhoods.json`.

## License

[MIT](LICENSE)
