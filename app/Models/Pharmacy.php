<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Pharmacy extends Model
{
    use HasFactory;

    protected $table = 'pharmacy';

    protected $fillable = ['user_id', 'description', 'image', 'name', 'email', 'phone', 'address', 'postcode', 'lat', 'lang', 'start_time', 'end_time', 'commission_amount', 'status', 'is_priority', 'is_shipping', 'delivery_charges', 'language'];

    protected $appends = ['fullImage'];

    public const STATUS_PENDING = 0;
    public const STATUS_APPROVED = 1;
    public const STATUS_REJECTED = 2;

    protected static array $statusMap = [
        'pending' => self::STATUS_PENDING,
        'approved' => self::STATUS_APPROVED,
        'rejected' => self::STATUS_REJECTED,
    ];

    protected function getFullImageAttribute()
    {
        return url('images/upload').'/'.$this->image;
    }

    public function getStatusAttribute($value)
    {
        if (is_numeric($value)) {
            $reverseMap = array_flip(self::$statusMap);
            return $reverseMap[(int) $value] ?? $value;
        }

        return $value;
    }

    public function setStatusAttribute($value)
    {
        if (is_string($value)) {
            $mapped = self::$statusMap[strtolower($value)] ?? $value;
            $this->attributes['status'] = $mapped;
            return;
        }

        $this->attributes['status'] = $value;
    }

    public function scopeGetByDistance($query, $lat, $lng, $radius)
    {
        if ($lat !== null && $lng !== null) {
            $results = DB::select(
                'SELECT id, (3959 * acos(cos(radians(?)) * cos(radians(lat)) * cos(radians(lang) - radians(?)) + sin(radians(?)) * sin(radians(lat)))) AS distance
                FROM pharmacy
                HAVING distance < ?
                ORDER BY distance',
                [$lat, $lng, $lat, $radius]
            );

            $ids = array_map(fn ($r) => $r->id, $results);

            return $query->whereIn('id', $ids);
        }

        return $query->whereIn('id', []);
    }
}
