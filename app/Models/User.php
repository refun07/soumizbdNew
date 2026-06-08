<?php

namespace App\Models;

use App\Models\Role;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasApiTokens, HasRoles, HasFactory, Notifiable, SoftDeletes;

    # The attributes that are mass assignable. 
    protected $guarded = ['id'];

    # The attributes that should be hidden for serialization. 
    protected $hidden = [
        'password',
        'remember_token',
    ];

    # The attributes that should be cast. 
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    # customers
    public  function scopeCustomers($query)
    {
        return $query->where('user_type', 'customer');
    }

    # staffs
    public  function scopeStaffs($query)
    {
        return $query->where('user_type', 'staff');
    }

    # sellers
    public  function scopeSellers($query)
    {
        return $query->where('user_type', 'seller');
    }

    # user shop
    public function shop()
    {
        return $this->hasOne(Shop::class, 'id', 'shop_id');
    }

    # role
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    # address
    public function addresses()
    {
        return $this->hasMany(UserAddress::class);
    }

    # order Group
    public function orderGroups()
    {
        return $this->hasMany(OrderGroup::class);
    }

    # orders
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    # carts
    public function carts($warehouseIds)
    {
        return $this->hasMany(Cart::class)->whereHas('productVariation')->whereIn('warehouse_id', $warehouseIds)->get();
    }

    # allCarts
    public function allCarts()
    {
        return $this->hasMany(Cart::class)->whereHas('productVariation');
    }

    # wishlists
    public function wishlists()
    {
        return $this->hasMany(Wishlist::class);
    }

    # productReviews
    public function productReviews()
    {
        return $this->hasMany(ProductReview::class);
    }

    # customerNoteLogs
    public function customerNoteLogs()
    {
        return $this->hasMany(CustomerNoteLog::class, 'customer_id', 'id');
    }

    # assignedTo
    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to', 'id');
    }

    # agentCommissionHistories
    public function agentCommissionHistories()
    {
        return $this->hasMany(AgentCommissionHistory::class, 'agent_id');
    }

    # scopeAgents
    public function scopeAgents($query)
    {
        return $query->whereIn('user_type', ['admin', 'staff']);
    }
}
