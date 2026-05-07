<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Reporting extends Model
{
        use Notifiable;
    protected $fillable = ['user_id', 'campaign_id', 'report_date', 'calls_count', 'success_count', 'dmc', 'comment'];

    // Relation avec l'agent
    public function user() {
        return $this->belongsTo(User::class);
    }

    // Relation avec la campagne
    public function campaign() {
        return $this->belongsTo(Campaign::class);
    }

    // Attribut calculé : Taux de transformation
    protected $appends = ['conversion_rate'];

    public function getConversionRateAttribute() {
        return $this->calls_count > 0 
            ? round(($this->success_count / $this->calls_count) * 100, 2) 
            : 0;
    }
}