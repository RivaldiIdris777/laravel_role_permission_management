# Laravel Roles and Permission using spatie + OTP

Website rule permission menggunakan spatie staterpack

Screenshot app:

![alt text](https://github.com/RivaldiIdris777/laravel_role_permission_management/blob/main/public/upload/sc_app/login.png?raw=true)

![alt text](https://github.com/RivaldiIdris777/laravel_role_permission_management/blob/main/public/upload/sc_app/role.png?raw=true)

![alt text](https://github.com/RivaldiIdris777/laravel_role_permission_management/blob/main/public/upload/sc_app/permission.png?raw=true)

![alt text](https://github.com/RivaldiIdris777/laravel_role_permission_management/blob/main/public/upload/sc_app/role_has_permission.png?raw=true)


### How to install

- jalankan perintah di cmd/terminal: `git clone https://github.com/RivaldiIdris777/laravel_role_permission_management.git`
- buatlah db baru dan masukkan di file `.env` pada `DB_DATABASE=` selanjutkan silahkan sesuaikan
```yaml
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nama_databasemu
DB_USERNAME=root
DB_PASSWORD=
```
- selanjutnya jalankan perintah di cmd/terminal `composer install` dan juga `composer update`
- selanjutnya jalankan perintah `php artisan storage:link`
- selanjutnya jalankan perintah `php artisan migrate`
- setelah itu jalankan perintah `php artisan db:seed` setelah itu jalankan `php artisan db:seed --RolePermissionSeeder`
- jalankan laravel dengan mengetik perintah `php artisan serve`
- jalankan laravel di browser dengan mengakses `localhost:8000`
- silahkan login dengan email `admin123@gmail.com` dan password `admin123`

## Fitur yang digunakan
- Menggunakan laravel breeze
- Menggunakan laravel spatie
- Menggunakan laravel intervention untuk compress gambar
- mengggunakan javascript untuk validasi kosong
- Menggunakan sweetalert javascript

## Lokasi storage file yang terlibat
- `AuthenticatedSessionController.php`
- Terdapat coding tambahan pada model `User.php`
- Terdapat code memanggil data langsung di file blade

## Alur pembuatan management spatie
- silahkan ikuti dari pembuatan crud pada role di setiap mvc
- lanjutkan dengan mengikuti pembuatan crud pada permission di setiap mvc
- lanjutkan dengan mengikuti pembuatan crud pada role has permission di setiap mvc
- lanjutkan dengan membuat crud pada user

## Cara menerapkan spatie
Pastikan anda telah membuat 2 akun yang sudah dibedakan dari permission dan role has permissionnya. dan menaruh rule pada akun pada crud usernya.
- gunakan kode berikut jika anda ingin menyembunyikan link akses halaman (tidak untuk jika langsung diakses di url), terapkan pada sidebar contoh:
```yaml
    @if(Auth::user()->can('pos.menu'))
        <li>
            <a href="{{ route('all.user') }}">
                <i class="bi bi-circle"></i><span>All Users</span>
            </a>
        </li>
    @endif
```
- jika ingin beberapa user boleh mengakses halaman atau langsung otomatis ke redirect ke halaman lain jika bukan hak aksesnya contoh:
```yaml
Route::get('/all/users', 'AllUser')->name('all.user')->middleware('permission:user.all');
```
- pada koding diatas dengan route `Route::get('/all/users', 'AllUser')->name('all.user');` ditambahkan `middleware('permission:user.all')` sehingga menjadi gabungan kode sesuai diatas.


# Penambahan fitur OTP

Website rule permission menggunakan spatie login menggunakan otp

Screenshot app feature otp

![alt text](https://github.com/RivaldiIdris777/laravel_role_permission_management/blob/feature_otp/public/upload/sc_app/otp_laravel.png?raw=true)

![alt text](https://github.com/RivaldiIdris777/laravel_role_permission_management/blob/feature_otp/public/upload/sc_app/mailtrapotp_laravel.png?raw=true)


### How to install

- Buatlah kolom baru pada tabel user dengan menjalankan perintah `php artisan make:migration add_otp_column_to_users_table` berikut dari isi field yang ditambahkan
```yaml
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('code')->nullable();
            $table->dateTime('expire_at')->nullable();
        });
    }
    
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('expire_at');
        });
    }
```
- selanjutnya jalankan perintah `php artisan:migrate`
- selanjutnya buatlah middleware untuk sistem otp dengan cara `php artisan make:middleware TwoFactor`
- selanjutnya buatlah controller untuk sistem otp dengan cara `php artisan make:controller TwoFactorController`
- selanjutnya buatlah notification untuk sistem otp dengan cara `php artisan make:notification TwoFactorCode`
- tambahkan kode paling bawah pada file `app\Http\Kernel.php` di `protected $middlewareAliases = []` yaitu `'two_factor' => \App\Http\Middleware\TwoFactor::class`
- tambahkan kode paling bawah pada file model `User.php` yaitu ...
```yaml
    public function generateCode() {
        $this->timestamps = false;
        $this->code = rand(10000, 99999);
        $this->expire_at = now()->addMinute(20);
        $this->save();
    }

    public function restCode() {
        $this->timestamps = false;
        $this->code = null;
        $this->expire_at = null;
        $this->save();
    }
```
- tambahkan code pada `route\web` yaitu `Route::resource('verify_otp', TwoFactorController::class);`
- masukkan code pada `Controllers/TwoFactorController` pada function `index` yaitu ....
```yaml
    public function index()
    {
        return view('auth.verify_otp');
    }
```
- masukkan code pada `Controllers\TwoFactorController` pada function `store` yaitu ....
```yaml
    $user = auth()->user(); 
        if ($request->input('code') == $user->code){
            $user->restCode();
            return redirect()->route('dashboard');
        }

    return redirect()->back()->withErrors(['code' => 'Please insert otp field']);
```
- masukkan code di `Middleware\TwoFactor.php` di dalam function `handle`
```yaml
    $user = auth()->user();
        if(auth()->check() && $user->code) {
            if(!$request->is('verify')) {
                return redirect()->route('verify_otp.index');
            }
        }
    return $next($request);
```
- masukkan code pada `app\Http\Requests\Auth\LoginRequest.php` di dalam function `public function authenticate(): void`
```yaml
        $this->ensureIsNotRateLimited();

        if (! Auth::attempt($this->only('email', 'password'), $this->boolean('remember'))) {
            RateLimiter::hit($this->throttleKey());

            throw ValidationException::withMessages([
                'email' => trans('auth.failed'),
            ]);
        }

        // use for otp
        $user = User::where('email',$this->input('email'))->first();
        $user->generateCode();

        $user->notify(new TwoFactorCode());
        // end of otp

        RateLimiter::clear($this->throttleKey());
```
- Buatlah file view di `views\auth\verify_otp.blade.php` (terlalu panjang, silahkan kunjungi filenya di projectnya)
- Silahkan tambahkan kode pada `.env` di field input yaitu ... (silahkan isi dengan sudah mendaftar di mailtrap)
```yaml
MAIL_MAILER=smtp
MAIL_HOST=sandbox.smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=tls
```
- Jalankan program, semoga lancar !!

