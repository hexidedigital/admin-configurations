<?php

namespace HexideDigital\AdminConfigurations\Models;

use App\Models\Traits\PositionSortTrait;
use App\Models\Traits\VisibleTrait;
use App\Models\Traits\WithTranslationsTrait;
use Astrotomic\Translatable\Translatable;
use HexideDigital\FileUploader\Facades\FileUploader;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminConfiguration
 *
 * @mixin AdminConfigurationTranslation
 * @property int $id
 * @property string $key
 * @property string $type
 * @property string|null $name
 * @property string|null $description
 * @property int $translatable
 * @property string|null $group
 * @property int $in_group_position
 * @property string|null $value
 * @property int $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\VariableTranslation|null $translation
 * @property-read Collection|\App\Models\VariableTranslation[] $translations
 * @property-read int|null $translations_count
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration joinTranslations(?string $modelTable = null, ?string $translationsTable = null, ?string $modelTableKey = null, ?string $translationsTableKey = null)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration listsTranslations(string $translationField)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration notTranslatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration query()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration sorted(string $direction = 'ASC', string $field = 'position')
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration translated()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration translatedIn(?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration visible()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereGroup($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereInGroupPosition($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereTranslatable($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration whereValue($value)
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration withTranslation()
 * @method static \Illuminate\Database\Eloquent\Builder|AdminConfiguration withTranslations()
 */
class AdminConfiguration extends Model
{
    use Translatable, WithTranslationsTrait;
    use VisibleTrait, PositionSortTrait;

    protected array $translatedAttributes = [
        'content'
    ];

    protected $fillable = [
        'key',
        'type',
        'name',
        'description',
        'translatable',
        'group',
        'in_group_position',
        'value',
        'status',
    ];

}
