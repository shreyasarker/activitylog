<?php

namespace ShreyaSarker\Activitylog\Tests\Support;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Model;
use ShreyaSarker\Activitylog\Tests\Support\Factories\TestUserFactory;

class TestUser extends Model
{
    use HasFactory;

    protected $table = 'test_users';

    protected $guarded = [];

    protected static function newFactory(): Factory
    {
        return TestUserFactory::new();
    }
}
