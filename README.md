# call-statistics-app

A simple app that loads Call data from a CSV file and shows call statistics

## Local Setup

#### Pre-conditions for local machine
* PHP version: 8
* MySQL version: 8

#### Setup

1 - Clone the repository
```
git clone git@github.com:mjbdelacruz/call-statistics-app.git
cd call-statistics-app/
```

2 - Run composer
```
composer install -o
```

3 - Update **.env** file and set the local env vars based on your needs
```
// Configure database:

// For local override change: db_user, db_password also database port connection in case it is not 3306
DATABASE_URL_READ="mysql://db_user:db_password@127.0.0.1:3306/commpeak?serverVersion=8"
DATABASE_URL_WRITE="mysql://db_user:db_password@127.0.0.1:3306/commpeak?serverVersion=8"

// Configure GeoLocation API Key
GEO_LOCATION_API_KEY="{replace_with_api_key}"
```

#### Getting Started

1 - Load database schema
```
// Change db_user and db_password
mysql -u{db_user} -p{db_password} < migrations/schema.sql
```

2 - Parse countryInfo.txt data (Downloaded from: http://download.geonames.org/export/dump/countryInfo.txt)
```
php bin/console app:country-info-parser
```

3 - Run the built-in PHP server

```
cd public/
php -S 0.0.0.0:8090
```

4 - Single page can now be viewed in the browser
```
http://0.0.0.0:8090/statistics
```