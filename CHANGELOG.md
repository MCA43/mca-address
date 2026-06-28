# Changelog

## [0.1.0] - 2026-06-28

### Added
- Country, city, district, neighborhood models, migrations, and admin CRUD
- Cascading JSON API for forms and `mca/settings` contact address fields
- Turkey seed: 81 cities and 972 districts (`mca:address:install`)
- Turkey neighborhood import from turkey-neighbourhoods (`mca:address:import-turkey`)
- Searchable filter selects, sortable table columns, sidebar edit form (permission-style)
- Hub integration (`extra.mca`), English and Turkish translations
- UAVT-ready columns and settings stub page

### Notes
- Neighborhood import requires districts seed first; JSON cache stored in app storage on first import
- 4 district name mismatches may skip a small number of neighborhoods until seed data is updated
