<?php

use Orchestra\Testbench\TestCase;

uses(TestCase::class);

test('base repository can be instantiated', function () {
    $abstract = Laravelplus\RepositoryPattern\BaseRepository::class;
    expect(class_exists($abstract))->toBeTrue();
});

test('base repository CRUD methods can be called', function () {
    $model = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
    $model->shouldReceive('getTable')->andReturn('test_table');
    $model->shouldReceive('all')->andReturn(new \Illuminate\Database\Eloquent\Collection([['id' => 1, 'name' => 'Test']]));
    $model->shouldReceive('where')->with('id', 1)->andReturnSelf();
    $firstModel = Mockery::mock(\Illuminate\Database\Eloquent\Model::class, ['id' => 1, 'name' => 'Test']);
    $firstModel->shouldReceive('offsetExists')->andReturn(false);
    $firstModel->shouldReceive('update')->andReturn(true);
    $model->shouldReceive('first')->andReturn($firstModel);
    $createdModel = new class extends \Illuminate\Database\Eloquent\Model {
        public $name = 'Created';
    };
    $model->shouldReceive('create')->andReturn($createdModel);
    $model->shouldReceive('update')->andReturn(true);
    $model->shouldReceive('delete')->andReturn(true);
    $model->shouldReceive('offsetExists')->andReturn(false);
    $repo = new class($model) extends \Laravelplus\RepositoryPattern\BaseRepository {
        protected string $modelClass = '';
    };
    expect($repo->all())->toBeIterable();
    expect($repo->find(1))->toBeObject();
    expect($repo->create(['name' => 'Created']))->name->toBe('Created');
    expect($repo->update(1, ['name' => 'Updated']))->not()->toBeNull();
    expect($repo->delete(1))->toBeTrue();
});

test('base repository hides fields', function () {
    $model = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
    $model->shouldReceive('getTable')->andReturn('test_table');
    $model->shouldReceive('all')->andReturn(new \Illuminate\Database\Eloquent\Collection([['id' => 1, 'name' => 'Test', 'secret' => 'hidden']]));
    $repo = new class($model) extends \Laravelplus\RepositoryPattern\BaseRepository {
        protected array $hidden = ['secret'];
    };
    $all = $repo->all();
    $all = $all instanceof \Illuminate\Database\Eloquent\Collection ? $all : new \Illuminate\Database\Eloquent\Collection($all);
    expect($all->first())->not()->toHaveKey('secret');
});

test('MultiDatabase trait runs callback on connection', function () {
    $trait = new class {
        use \Laravelplus\RepositoryPattern\Traits\MultiDatabase;
    };
    $called = false;
    $result = $trait->runOnConnection('sqlite', function ($db) use (&$called) {
        $called = true;
        return 'ok';
    });
    expect($result)->toBe('ok');
    expect($called)->toBeTrue();
});

test('Cacheable trait caches all results', function () {
    $model = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
    $model->shouldReceive('getTable')->andReturn('test_table');
    $model->shouldReceive('all')->andReturn(new \Illuminate\Database\Eloquent\Collection([['id' => 1]]));
    $repo = new class($model) extends \Laravelplus\RepositoryPattern\BaseRepository {
        use \Laravelplus\RepositoryPattern\Traits\Cacheable;
    };
    \Illuminate\Support\Facades\Cache::shouldReceive('remember')->andReturn(collect([['id' => 1]]));
    $all = $repo->cacheAll();
    expect($all)->toBeIterable();
});

test('SoftDeletes trait soft deletes and restores', function () {
    $model = Mockery::mock(\Illuminate\Database\Eloquent\Model::class);
    $model->shouldReceive('getTable')->andReturn('test_table');
    $model->shouldReceive('where')->with('id', 1)->andReturnSelf();
    $model->shouldReceive('update')->with(Mockery::on(function($arg) { return is_array($arg) && array_key_exists('deleted_at', $arg); }))->andReturn(true);
    $model->shouldReceive('update')->with(['deleted_at' => null])->andReturn(true);
    $model->shouldReceive('whereNotNull')->with('deleted_at')->andReturnSelf();
    $model->shouldReceive('get')->andReturn(collect([['id' => 1, 'deleted_at' => now()]]));
    $repo = new class($model) extends \Laravelplus\RepositoryPattern\BaseRepository {
        use \Laravelplus\RepositoryPattern\Traits\SoftDeletes;
        protected string $primaryKey = 'id';
    };
    expect($repo->softDelete(1))->toBeTrue();
    expect($repo->restore(1))->toBeTrue();
    $trashed = $repo->onlyTrashed();
    expect($trashed)->toBeIterable();
}); 