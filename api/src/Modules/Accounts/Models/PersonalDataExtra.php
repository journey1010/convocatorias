<?php

namespace Modules\Accounts\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\User\Models\User;
use Modules\Ubigeo\Models\{Department, Province, District};

class PersonalDataExtra extends Model
{
    use HasFactory;

    protected $table = 'personal_data_extra';

    protected $fillable = [
        'user_id',
        'department_id',
        'province_id',
        'district_id',
        'ruc',
        'address',
        'birthday',
        'gender',
        'file_dni',
        'have_cert_disability',
        'file_cert_disability',
        'have_cert_army',
        'file_cert_army',
        'have_cert_professional_credentials',
        'file_cert_professional_credentials',
        'is_active_cert_professional_credentials',
    ];

    protected function casts(): array
    {
        return [
            'birthday' => 'date',
            'have_cert_disability' => 'boolean',
            'have_cert_army' => 'boolean',
            'have_cert_professional_credentials' => 'boolean',
            'is_active_cert_professional_credentials' => 'boolean',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function province(): BelongsTo
    {
        return $this->belongsTo(Province::class);
    }

    public function district(): BelongsTo
    {
        return $this->belongsTo(District::class);
    }
}
