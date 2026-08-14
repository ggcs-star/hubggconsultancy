<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class CertificateTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'design_type',
        'preview_image',
        'background_image',
        'signature_image',
        'signer_name',
        'signer_designation',
        'organization_name',
        'settings',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
    ];

    /*
    |--------------------------------------------------------------------------
    | Courses
    |--------------------------------------------------------------------------
    */

    public function courses(): HasMany
    {
        return $this->hasMany(
            Course::class,
            'certificate_template_id'
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Preview
    |--------------------------------------------------------------------------
    */

    public function previewUrl(): ?string
    {
        if (empty($this->preview_image)) {
            return null;
        }

        return Storage::disk('public')->url(
            $this->preview_image
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Background
    |--------------------------------------------------------------------------
    */

    public function backgroundUrl(): ?string
    {
        if (empty($this->background_image)) {
            return null;
        }

        return Storage::disk('public')->url(
            $this->background_image
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Signature
    |--------------------------------------------------------------------------
    */

    public function signatureUrl(): ?string
    {
        if (empty($this->signature_image)) {
            return null;
        }

        return Storage::disk('public')->url(
            $this->signature_image
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Company Details
    |--------------------------------------------------------------------------
    */

    public function companyDetails(): array
    {
        $company = $this->settings['company'] ?? [];

        return [
            'name' => $company['name']
                ?? $this->organization_name
                ?? 'Global Garner',

            'address' => $company['address']
                ?? '',

            'phone' => $company['phone']
                ?? '',

            'email' => $company['email']
                ?? '',

            'website' => $company['website']
                ?? '',
        ];
    }
}