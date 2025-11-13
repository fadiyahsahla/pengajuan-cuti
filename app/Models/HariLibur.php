<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class HariLibur extends Model
{
    protected $table = 'hari_libur';

    protected $fillable = [
        'tanggal',
        'nama_libur',
        'tahun',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    public static function isHoliday($date)
    {
        return self::whereDate('tanggal', Carbon::parse($date))->exists();
    }

    public static function getHolidaysBetween($startDate, $endDate)
    {
        return self::whereBetween('tanggal', [$startDate, $endDate])->get();
    }
}
