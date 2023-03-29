<?php

namespace App\Models;

use App\Models\Token;
use Laravel\Cashier\Billable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable{
    
    use Notifiable, SoftDeletes, Billable;

    const ADMIN    = 'ADMIN';
    const AGENCY   = 'AGENCY';
    const CUSTOMER = 'CUSTOMER';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'role', 'parent_id', 'allow_lifetime', 'country_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * Send the password reset notification.
     *
     * @param  string  $token
     * @return void
     */
    public function sendPasswordResetNotification($token)
    {
        $this->notify(new ResetPasswordNotification($token));
    }

    public function agency(){
        return $this->belongsTo(User::class, 'parent_id');
    }

    public function customers(){
        return $this->hasMany(User::class, 'parent_id');
    }

    public function purchased_plans() {
        return $this->belongsToMany(Plan::class, "customer_plan");
    }

    public function key()
    {
        return $this->hasOne(UserKey::class);
    }

    public function token()
    {
        return $this->hasOne(Token::class);
    }

    public function orders() {
        return $this->hasMany(Order::class);
    }

    public function platform_bank_account()
    {
        return $this->hasOne(PlatformBankAccount::class);
    }

    public function bank_account()
    {
        return $this->hasOne(BankAccount::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function plugin()
    {
        return $this->hasOne(Plugin::class);
    }

    /**
     * Un usuario(customer) tiene una compañia
     */
    public function company()
    {
        return $this->hasOne(Company::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    // Functions for validates
    public function isAdmin(){
        return $this->role == User::ADMIN;
    }

    public function isAgency(){
        return $this->role == User::AGENCY;
    }

    public function isCustomer(){
        return $this->role == User::CUSTOMER;
    }

    #Relación con el modulo [Logs]
    public function log() { //Un Usuario puede terner muchos logs
        return $this->hasMany(Log::class);
    }

    # Relación con tarjetas
    // @author Matías
    public function cards(){
        // Relación de N -> M (uno a muchos) siendo N usuarios y M tarjetas - cards
        return $this->hasMany(UserCards::class);
    }
}
