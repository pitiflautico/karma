<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToOrganizationScope;

class Setting extends Model
{
    use HasFactory, HasUuids, BelongsToOrganizationScope;

    /**
     * Indicates if the model's ID is auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'settings';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'organization_id',
        'company_name',
        'company_address',
        'company_phone',
        'company_email',
        'company_website',
        'logo_path',
        'vat_number',
        'invoice_prefix',
        'invoice_sequence',
        'default_currency',
        'tax_iva',
        'tax_irpf',
        'goal',
        'legal_text_invoice',
        'created_at',
        'updated_at',
    ];

    /**
     * Get the user that owns the settings.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the organization that the settings belong to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
