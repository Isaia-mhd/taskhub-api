<?php

namespace App\Traits;

use Illuminate\Support\Str;

trait HasUniqueSlug
{
    public static function generateSlug(string $value, $column = 'slug')
    {
        $slug = Str::slug($value);
        $originalSlug = $slug;
        $count = 1;

        while (static::where($column, $slug)->exists()) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }
}
