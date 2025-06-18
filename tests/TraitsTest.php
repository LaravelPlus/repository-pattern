<?php

namespace Laravelplus\RepositoryPattern\Tests;

use Illuminate\Database\Eloquent\Model;
use Laravelplus\RepositoryPattern\BaseRepository;
use Laravelplus\RepositoryPattern\Traits\Searchable;
use Laravelplus\RepositoryPattern\Traits\SoftDeletes;
use Laravelplus\RepositoryPattern\Traits\Sortable;
use Laravelplus\RepositoryPattern\Traits\ValidatesData;
use PHPUnit\Framework\TestCase;

class TraitsTest extends TestCase
{
    public function testTraitsCanBeUsedTogether(): void
    {
        $repository = new class extends BaseRepository {
            use Searchable;
            use SoftDeletes;
            use Sortable;
            use ValidatesData;

            public function model(): Model
            {
                return new class extends Model {
                    protected $table = 'test_table';
                };
            }
        };

        $this->assertInstanceOf(BaseRepository::class, $repository);
        $this->assertTrue(method_exists($repository, 'search'));
        $this->assertTrue(method_exists($repository, 'withTrashed'));
        $this->assertTrue(method_exists($repository, 'sortBy'));
        $this->assertTrue(method_exists($repository, 'validate'));
    }
} 