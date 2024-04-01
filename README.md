# Geo locations data
A JSON library to display countries, states, counties and cities with PHP. 

NOTE: This package is under development and should not use ID values as final

## Usage
```php
use Dantepiazza\GeoLocationData\Location;

$location = new Location();

// Regenerata a chache file
$location -> cache();

// Search locations by city name
$location -> search('Buenos Ai');

// Get location by city name
$location -> get(6344);

// Returns only the list of countries
$location -> countries();

// Get country by id
$location -> country('id', 11);

// Returns only the list of states
$location -> states();

// Get state by id
$location -> state(788);

// Returns only the list of counties in the country code
$location -> counties('AR');

// Get county by id
$location -> county('AR', 216);

// Returns only the list of cities in the country code
$location -> cities('AR');

// Get city by id
$location -> city('AR', 4867);
```

## Responses

##### Location data
```php
country_id: int
country_name: string
country_code: string
state_id: int
state_name: string
county_id: int
county_name: string
city_id: int
city_name: string
zip_code: string
name: string
```

##### Country data
```php
id: int
code: string
name: string
```

##### State data
```php
id: int
country_id: int
name: string
```

##### County data
```php
id: int
country_id: int
state_id: int
name: string
```

##### City data
```php
id: int
country_id: int
state_id: int
county_id: int
zip_code: string
name: string
```