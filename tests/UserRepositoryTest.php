<?php

declare(strict_types=1);

use Illuminate\Database\Capsule\Manager as Capsule;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Schema\Blueprint;
use Prefabs\UserRepository;

describe('UserRepository', function (): void {
    beforeEach(function (): void {
        // Setup Eloquent with in-memory SQLite
        $capsule = new Capsule();
        $capsule->addConnection([
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
        $capsule->setAsGlobal();
        $capsule->bootEloquent();

        // Create users table
        Capsule::schema()->create('users', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->timestamps();
        });
    });

    it('can create, find, update, delete, and search users', function (): void {
        // Define a simple User model
        eval('class TestUser extends \\Illuminate\\Database\\Eloquent\\Model { protected $table = "users"; protected $fillable = ["name", "email", "password"]; public $timestamps = true; }');
        $userModel = new TestUser();
        $repo = new UserRepository($userModel);

        // Create
        $user = $repo->create([
            'name' => 'Alice',
            'email' => 'alice@example.com',
            'password' => 'secret',
        ]);
        expect($user->id)->not()->toBeNull();
        expect($user->name)->toBe('Alice');
        expect(password_verify('secret', $user->password))->toBeTrue();

        // Find
        $found = $repo->find($user->id);
        expect($found)->not()->toBeNull();
        expect($found->email)->toBe('alice@example.com');

        // Find by email
        $foundByEmail = $repo->findBy('email', 'alice@example.com');
        expect($foundByEmail)->not()->toBeNull();
        expect($foundByEmail->id)->toBe($user->id);

        // Update
        $repo->update($user->id, ['name' => 'Alice Updated']);
        $updated = $repo->find($user->id);
        expect($updated->name)->toBe('Alice Updated');

        // Search
        $results = $repo->search('Alice');
        expect($results)->toHaveCount(1);
        expect($results->first()->id)->toBe($user->id);

        // All
        $all = $repo->all();
        expect($all)->toHaveCount(1);

        // Delete
        $deleted = $repo->delete($user->id);
        expect($deleted)->toBeTrue();
        expect($repo->find($user->id))->toBeNull();
    });
});
