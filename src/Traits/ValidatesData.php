<?php

declare(strict_types=1);

namespace Laravelplus\RepositoryPattern\Traits;

use Illuminate\Support\Facades\Validator;

trait ValidatesData
{
    protected array $rules = [];

    public function validate(array $data)
    {
        return Validator::make($data, $this->rules)->validate();
    }
} 