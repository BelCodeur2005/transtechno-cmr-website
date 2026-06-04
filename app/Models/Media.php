<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Media extends Model {
    protected $fillable = ["filename","path","alt","title","caption","description","mime_type","size"];
}