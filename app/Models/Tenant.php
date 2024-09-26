<?php

declare(strict_types=1);

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Stancl\Tenancy\Contracts;
use Stancl\Tenancy\Database\Concerns;
use Stancl\Tenancy\Database\TenantCollection;
use Stancl\Tenancy\Events;

/**
 * Class Tenant
 *
 * Represents a tenant in a multi-tenant system.
 *
 * @package App\Models
 * @property string|int $id The unique identifier for the tenant.
 * @property Carbon $created_at The date and time when the tenant was created.
 * @property Carbon $updated_at The date and time when the tenant was last updated.
 * @property array $data Custom data associated with the tenant.
 *
 * @method static TenantCollection all($columns = ['*']) Retrieve all tenants.
 */
class Tenant extends Model implements Contracts\Tenant
{
    use Concerns\CentralConnection,
        Concerns\GeneratesIds,
        Concerns\HasDataColumn,
        Concerns\HasInternalKeys,
        Concerns\TenantRun,
        Concerns\InvalidatesResolverCache;

    protected static $modelsShouldPreventAccessingMissingAttributes = false;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'tenants';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Get the name of the "tenant key" column.
     *
     * @return string
     */
    public function getTenantKeyName(): string
    {
        return 'id';
    }

    /**
     * Get the custom columns for the tenant model.
     *
     * @return array
     */
    public static function getCustomColumns(): array
    {
        return [
            'id',
            'name',
        ];
    }

    /**
     * Get the value of the tenant key.
     *
     * @return mixed
     */
    public function getTenantKey()
    {
        return $this->getAttribute($this->getTenantKeyName());
    }

    /**
     * Create a new Eloquent Collection instance.
     *
     * @param array $models
     * @return TenantCollection
     */
    public function newCollection(array $models = []): TenantCollection
    {
        return new TenantCollection($models);
    }

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'saving' => Events\SavingTenant::class,
        'saved' => Events\TenantSaved::class,
        'creating' => Events\CreatingTenant::class,
        'created' => Events\TenantCreated::class,
        'updating' => Events\UpdatingTenant::class,
        'updated' => Events\TenantUpdated::class,
        'deleting' => Events\DeletingTenant::class,
        'deleted' => Events\TenantDeleted::class,
    ];

    /**
     * Returns the administrator for the tenant.
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasOne
     */
    public function members()
    {
        return $this->hasMany(User::class);
    }
}
