<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivesIntegrations extends Model
{
    use HasFactory;

    public $guarded = [];

    protected $table = "archives_integrations";
}
