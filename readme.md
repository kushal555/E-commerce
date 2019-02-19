## E-commerce Using Laravel and VueJS 

This source code contain the below functionalities
1. Login using JWT auth
1. Show all the products (Listing product)
1. Products listing should have pagination 
1. Provide a form to add the Product and also need a way to upload the product image

## Requirements

- [Laravel server requirements](https://laravel.com/docs/5.5#server-requirements)
- Laravel 5.5 requires PHP 7.1 or later

## Setting up the project

1. Clone the repository
1. Run `composer install`
1. Configure Laravel directory permissions ([documentation](https://laravel.com/docs/5.5))
1. Create a database and setup the database configuration inside `.env`
1. Run `php artisan key:generate`
1. Run `php artisan migrate`
1. If you have the below error while doing migration
   ![Database Error](https://github.com/kushal555/E-commerce/blob/master/Database-error.PNG)
   Just put the below code in your `AppServiceProvider.php` file
   ```php
   use Illuminate\Support\Facades\Schema;

   public function boot()
   {
       Schema::defaultStringLength(191);
   }
   ````
1. Delete the all tables from Database and run again the command `php artisan migrate`
1. Run `php artisan jwt:secret`
1. Run `npm install`
1. Use `php artisan tinker` to create a test user:
```
App\User::create(['name' => 'Test User', 'email' => 'test@email.com', 'password' => Hash::make('Password01')])
```
1. Setup the configuration file for VueJS, So just need to create `env.js` file inside the `resources/assets/js/` Directory. Put the below code in it and replace your folder name of project
```javascript

const configuration = {
    api_url : "http://localhost/{project-directory-name}/public/api",
    api_version : '/v1'
}

export default configuration
```

## Build
- Once you have made changes to the JavaScript or SCSS files you will want to run `npm run dev` or `npm run production`


