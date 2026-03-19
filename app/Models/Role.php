<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Spatie\Permission\Models\Role as SpatieRole;

#[Table(key: 'uuid')]
class Role extends SpatieRole
{
    use HasUuids;
}
