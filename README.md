## Setup Instructions

Install the necessary dependencies using composer:

`composer install`

Configure the database connection by updating the '.env' fle with your MySQL credentials
Perform the database migrations:

`php artisan migrate`

Start the applications:

`php artisan serve`

The application should now be running on 'http://localhost:8000'

All the apis has 'employee' prefix.

`[GET] http://127.0.0.1:8000/api/employee/` : Get all datas from database.
`[GET] http://127.0.0.1:8000/api/employee/{id}` : Show selected id employee info.
`[DELETE] http://127.0.0.1:8000/api/employee/{id}` : Delete selected id employee from the database.
