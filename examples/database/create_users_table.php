<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Schema\Blueprint;

// Create the users table
Capsule::schema()->create('users', function (Blueprint $table): void {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->string('password');
    $table->timestamps();
    $table->softDeletes();
});
