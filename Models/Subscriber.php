<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Subscriber extends Model
{
    use HasFactory;
    protected $fillable =
        [
            'email'
        ];

    public function getFillable(): array
    {
        return $this->fillable;
    }

    public function setFillable(array $fillable): void
    {
        $this->fillable = $fillable;
    }
}
