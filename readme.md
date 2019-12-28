<p align="center"><img src="https://laravel.com/assets/img/components/logo-laravel.svg"></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/d/total.svg" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/v/stable.svg" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://poser.pugx.org/laravel/framework/license.svg" alt="License"></a>
</p>

## This project

Starting point to create a webapp with:

- [A Laravel 5.8 backend](https://laravel.com/docs/5.8/)
- [Vue frontend](https://vuejs.org/)
- [Vuetify framework](https://vuetifyjs.com/en/)
- [Typescript class](https://github.com/vuejs/vue-class-component) and [property decorators](https://github.com/kaorun343/vue-property-decorator)

Uses Laravel passport to authenticate


Task scheduling
* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1

## Getting started

Clone the repository and copy the .env.example to .env file to connect to your database. Run ``` composer install ``` and ```npm install```. You should be connected to a database. Run ```php artisan migrate``` to create the tables.  
When you're done migrating run ```php artisan passport:install``` to generate the laravel passport tokens. These should go in your .env file aswell. 

You can allways find the secret in the oauth_clients table from the Laravel Password Grant Client. It's usually id 2. Enter that in your .env.

To run the app run ```npm run dev``` or ```npm run watch``` 


php artisan passport:install
php artisan passport:client --personal