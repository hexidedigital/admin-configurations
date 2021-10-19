<?php

namespace HexideDigital\AdminConfigurations\Models;

use App\Models\Traits\PositionSortTrait;
use App\Models\Traits\VisibleTrait;
use App\Models\Traits\WithTranslationsTrait;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminConfiguration
 *
 * @mixin \HexideDigital\AdminConfigurations\Models\AdminConfigurationTranslation
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
 * @property-read \HexideDigital\AdminConfigurations\Models\AdminConfigurationTranslation|null $translation
 * @property-read Collection|\HexideDigital\AdminConfigurations\Models\AdminConfigurationTranslation[] $translations
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


    public const type_TITLE = 'title'; // short text /input - type=text
    public const type_TEXT  = 'text';  // long text /textarea
    public const type_IMAGE = 'image'; // image /input - file select

    public const types = [
        self::type_TITLE,
        self::type_TEXT,
        self::type_IMAGE,
    ];

    public static function getTypes(): array
    {
        return self::types;
    }

    public static function item(string $type, string $key, string $name, ?bool $translatable = null, $value = null): array
    {
        $item = [
            'key' => $key,
            'type' => $type,
            'name' => $name,
        ];

        if (!empty($translatable)) {
            $item['translatable'] = true;
            if(is_array($value)) {
                foreach (config('app.locales') as $locale) {
                    $item[$locale] = ['content' => $value[$locale] ?? ''];
                }
            }else{
                foreach (config('app.locales') as $locale){
                    $item[$locale] = ['content' => $value??''];
                }
            }
        }else if(!empty($value)){
            $item['value'] = $value;
        }

        return $item;
    }

    public static function textItem(string $key, string $name, ?bool $translatable = null, $value = null): array
    {
        return self::item(self::type_TEXT, $key, $name, $translatable, $value);
    }
    public static function titleItem(string $key, string $name, ?bool $translatable = null, $value = null): array
    {
        return self::item(self::type_TITLE, $key, $name, $translatable, $value);
    }
    public static function imageItem(string $key, string $name, ?bool $translatable = null, $value = null): array
    {
        return self::item(self::type_IMAGE, $key, $name, $translatable, $value);
    }


    /**
     * @param string|array $name
     * @return array
     */
    public static function var_groups($name = []): array
    {
        if (!is_array($name)) {
            $name = array($name);
        }

        $data = AdminConfiguration::visible()
            ->whereIn('group', $name)
            ->orderBy('in_group_position')
            ->get();

        return self::makeVariablesMap($data);
    }

    public static function makeVariablesMap(Collection $array): array
    {
        $data = [];
        /** @var AdminConfiguration $admin_configuration */
        foreach ($array as $admin_configuration) {
            $value = $admin_configuration->translatable
                ? ($admin_configuration->type === self::type_IMAGE
                    ? asset($admin_configuration->translate(app()->getLocale())->content??'')
                    : $admin_configuration->translate(app()->getLocale())->content ?? ''
                )
                : $admin_configuration->value ?? '';

            $data[$admin_configuration->group][$admin_configuration->key][] = $value;
        }
        return $data;
    }


}
