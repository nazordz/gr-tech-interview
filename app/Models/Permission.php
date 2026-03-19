<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Permission as SpatiePermission;

#[Table(key: 'uuid')]
class Permission extends SpatiePermission
{
    use HasUuids;
}
