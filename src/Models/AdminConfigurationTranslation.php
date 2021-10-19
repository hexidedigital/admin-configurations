<?php

namespace HexideDigital\AdminConfigurations\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * HexideDigital\AdminConfigurations\Models\AdminConfigurationTranslation
 *
 * @property int $id
 * @property string $locale
 * @property int $variable_id
 * @property string|null $content
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation whereContent($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation whereLocale($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfigurationTranslation whereVariableId($value)
 */
class AdminConfigurationTranslation extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'locale', 'admin_configuration_id',
        'content',
    ];

}
