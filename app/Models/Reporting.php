<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reporting extends Model
{
        use Notifiable;
    protected $fillable = ['user_id', 'campaign_id', 'report_date', 'calls_count', 'success_count', 'dmc', 'comment'];

    // Relation avec l'agent

    // Dans Reporting.php
protected $appends = ['conversion_rate'];

public function getConversionRateAttribute()
{
    if ($this->calls_count <= 0) return 0;
    return round(($this->success_count / $this->calls_count) * 100, 2);
}
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relation avec la campagne
    public function campaign() {
        return $this->belongsTo(Campaign::class);
    }

    

    // Attribut calculé : Taux de transformation
}