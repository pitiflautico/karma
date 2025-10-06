<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Concerns\BelongsToOrganizationScope;

/**
 * App\Models\InvoiceItem
 *
 * @property int $id
 * @property string $uuid
 * @property int $user_id
 * @property int $invoice_id
 * @property int $organization_id
 * @property string $description
 * @property int $quantity
 * @property int $unit_price
 * @property int $tax_rate
 * @property int $total_line
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Invoice $invoice
 * @property-read \App\Models\Organization $organization
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\InvoiceItemFactory factory(...$parameters)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem findSimilarSlugs($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem query()
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereInvoiceId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereOrganizationId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTaxRate($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereTotalLine($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUnitPrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem whereUuid($value)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem withUnique($attribute, $config, $slug)
 * @method static \Illuminate\Database\Eloquent\Builder|InvoiceItem withoutTrashed()
 * @mixin \Eloquent
 */
class InvoiceItem extends Model
{
    use HasFactory, HasUuids, BelongsToOrganizationScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'invoice_id',
        'organization_id',
        'description',
        'quantity',
        'unit_price',
        'price',
        'tax_rate',
        'tax_total',
        'irpf_rate',
        'irpf_total',
        'total',
        'total_line',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'integer',
        'price' => 'integer',
        'tax_rate' => 'integer',
        'tax_total' => 'integer',
        'irpf_rate' => 'decimal:2',
        'irpf_total' => 'integer',
        'total' => 'integer',
        'total_line' => 'integer',
    ];

    /**
     * Get the user who created this invoice item.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the invoice this item belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Get the organization that the invoice item belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }
}
