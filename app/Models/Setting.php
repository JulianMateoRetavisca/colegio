<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class Setting extends Model
{
    protected $table = 'settings';
    protected $fillable = ['key','value','type','updated_by'];

    public $timestamps = true;

    public static function getValue(string $key, $default = null)
    {
        if (!Schema::hasTable('settings')) {
            return $default;
        }
        return Cache::rememberForever("setting:".$key, function() use ($key, $default) {
            $row = static::where('key',$key)->first();
            return $row ? $row->value : $default;
        });
    }

    public static function setValue(string $key, ?string $value, ?int $updatedBy = null, ?string $type = null)
    {
        Cache::forget("setting:".$key);
        return static::updateOrCreate(['key'=>$key], [
            'value' => $value,
            'type' => $type,
            'updated_by' => $updatedBy,
        ]);
    }

    public static function getMany(array $defaults): array
    {
        if (!Schema::hasTable('settings')) {
            return $defaults;
        }
        $out = [];
        foreach ($defaults as $k=>$v) { $out[$k] = static::getValue($k, $v); }
        return $out;
    }
}
