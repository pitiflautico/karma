<?php

namespace App\Models;

use App\Enums\Invoice\StatusEnum;
use App\Models\Concerns\BelongsToOrganizationScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model
{
    use HasFactory, HasUuids, SoftDeletes, BelongsToOrganizationScope;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'client_id',
        'organization_id',
        'prefix',
        'number',
        'date_issued',
        'due_date',
        'total',
        'status',
        'paid_at',
        'created_at',
        'updated_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'date_issued' => 'datetime',
        'due_date' => 'datetime',
        'total' => 'integer',
        'status' => StatusEnum::class,
        'paid_at' => 'datetime',
        'number' => 'integer',
    ];

    /**
     * Get the user who created this invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the client this invoice belongs to.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function client(): BelongsTo
    {
        return $this->belongsTo(Client::class);
    }

    /**
     * Get the line items for this invoice.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * Get the organization that the invoice belongs to.
     */
    public function organization(): BelongsTo
    {
        return $this->belongsTo(Organization::class);
    }

    /**
     * Get the full invoice number (number/prefix or just number)
     */
    public function getInvoiceNumberAttribute(): string
    {
        if (empty($this->prefix)) {
            return (string) $this->number;
        }
        
        return $this->number . '/' . $this->prefix;
    }

    /**
     * Generate the next invoice number for a given prefix
     */
    public static function getNextNumber(?string $prefix = ''): int
    {
        $prefix = $prefix ?? '';
        
        $lastInvoice = static::where('prefix', $prefix)
            ->orderBy('number', 'desc')
            ->first();

        return $lastInvoice ? $lastInvoice->number + 1 : 1;
    }

    /**
     * Boot method to auto-generate number if not provided
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($invoice) {
            if (empty($invoice->number)) {
                $invoice->number = static::getNextNumber($invoice->prefix);
            }
        });
    }
}
