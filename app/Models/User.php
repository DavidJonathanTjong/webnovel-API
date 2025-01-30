<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'users'; 
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function scopeFiltered(Builder $builder){
        $search = request('search') ?? null;
        $searchColumns = request('searchColumns') ?? null;

        $sort = request('sort') ?? null;
        $sortBy = request('sortBy') ?? null;
        $sortColumns = request('sortColumns') ?? null;

        $users = $builder ->select(
            'users.id AS id',
            'users.name AS name',
            'users.email AS email',
            'users.password AS password'
        );

        if($search && Str::length($search)>0){
            $listSearch = Str::of($search)->split('/[\s,]+/')->toArray();
            $search = count($listSearch) > 0 ? implode("%", $listSearch) : "%($search)%";

            $searchColumns = Str::of($searchColumns)->split('/[\s,]+/')->toArray();

            $users ->where(function($query) use ($search, $searchColumns){
                foreach ($searchColumns as $searchColumn) {
                    $query -> orWhereRaw("users.{$searchColumn} LIKE '{$search}'");
                }
            });
        }

        $sortColumns = Str::of($sortColumns)->split('/[\s,]+/')->toArray();
        if(collect($sortColumns)->contains($sortBy) && collect(['ASC','DESC'])->contains($sort)){
            $users -> orderBy("users.{$sortBy}", $sort);
        }
        return $users;
    }
}