## About FSA Package

FSA Package stands for Full Sanctum Authentication Package 

## FSA Features:
- Must Verify Email.
- Can Reset Password.
- Socialite Login.

## Installation Guide

```bash
composer require tarek/fsa
php artisan vendor:publish --provider="Tarek\Fsa\FSAServiceProvider" or php artisan vendor:publish --tag=fsa
php artisan migrate
```
<hr>

These credentials should be placed in your application's `config/services.php` configuration file, depending on the providers your application requires
<br>
for Example
```injectablephp
'<your-provider>' => [
    'client_id' => env('<your-provider>_CLIENT_ID'),
    'client_secret' => env('<your-provider>_CLIENT_SECRET'),
    'redirect' => env('<your-provider>_REDIRECT'),
],
```
To reference your `<your-provider>_CLIENT_ID` and `<your-provider>_CLIENT_SECRET` and `<your-provider>_REDIRECT`
you have to adapt your `.env` file and set your keys and values from your providers <br>
for Example:
```ini
GOOGLE_CLIENT_ID=xyz
GOOGLE_CLIENT_SECRET=123
GOOGLE_REDIRECT=<your-domain>/api/auth/<your-provider>/callback
```
<hr>

Afterwards, include the authentication routes in your `route/api.php` using:
```injectablephp
require __DIR__ . '/Authentication/authentication.php';
```

Afterwards, Implement the following implementation in your `Models/User.php` Model using:
```injectablephp
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\CanResetPassword as CanResetPasswordImplementation;

implements MustVerifyEmail, CanResetPasswordImplementation
```

Then use Traits
```injectablephp
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Auth\Passwords\CanResetPassword;
use Tarek\Fsa\Traits\Providers;
use Tarek\Fsa\Traits\CheckEmailVerifyImplementation;

use HasApiTokens, Notifiable, CanResetPassword, Providers, CheckEmailVerifyImplementation;
```

<hr>

## License

The FSA Package is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
