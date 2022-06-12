
## Hood Application

### Requirements
In order to be able to run this application, you will need the following:
* PHP >= 8.0 with the phpredis extension installed.
* A Redis server for queues
* Mysql

### Installation
1. Clone this git repository to a local folder and cd into the folder.
2. Install the packages with composer
```bash
composer install
```
3. Rename `.env.example` to `.env` and enter your database credentials
4. Generate an app key
```bash
php artisan key:generate
```
3. Run the migrations
```bash
php artisan migrate
```
4. Link the public storage folder to the public folder.
```bash
php artisan storage:link
```
5. Start the services.
```bash
php artisan serve
php artisan queue:work
php artisan websockets:serve
```
Then proceed with installation of the [front end git respository](https://github.com/aleahy/hood-front).
