
## Hood Application

### Requirements
In order to be able to run this application, you will need the following:
* PHP >= 8.0 with the phpredis extension installed.
* A Redis server for queues
* Mysql

### Installation
1. Clone this git repository to a local folder.
2. Rename `.env.example` to `.env` and enter your database credentials
3. Generate an app key
```bash
php artisan key:generate
```
3. Run the migrations
```bash
php artisan migrate
```
4. Start the services.
```bash
php artisan serve
php artisan queue:work
php artisan websockets:serve
```
Then proceed with installation of the front end git respository.
