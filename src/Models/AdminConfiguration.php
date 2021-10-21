<?php

namespace HexideDigital\AdminConfigurations\Models;

use Arr;
use HexideDigital\HexideAdmin\Contracts\WithTypesContract;
use HexideDigital\HexideAdmin\Models\Traits\PositionSortTrait;
use HexideDigital\HexideAdmin\Models\Traits\VisibleTrait;
use HexideDigital\HexideAdmin\Models\Traits\WithTranslationsTrait;
use HexideDigital\HexideAdmin\Models\Traits\WithTypes;
use Illuminate\Database\Eloquent\Builder;
use Astrotomic\Translatable\Translatable;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\AdminConfiguration
 *
 * @method static array itemText(string $key, string $name, ?bool $translatable = null, $value = null)
 * @method static array itemTitle(string $key, string $name, ?bool $translatable = null, $value = null)
 * @method static array itemImage(string $key, string $name, ?bool $translatable = null, $value = null)
 *
 * @mixin \HexideDigital\AdminConfigurations\Models\AdminConfigurationTranslation
 * @mixin \Eloquent
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
 * @method static Builder|self joinTranslations(?string $modelTable = null, ?string $translationsTable = null, ?string $modelTableKey = null, ?string $translationsTableKey = null)
 * @method static Builder|self listsTranslations(string $translationField)
 * @method static Builder|self newModelQuery()
 * @method static Builder|self newQuery()
 * @method static Builder|self notTranslatedIn(?string $locale = null)
 * @method static Builder|self orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|self orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|self orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|self query()
 * @method static Builder|self sorted(string $direction = 'ASC', string $field = 'position')
 * @method static Builder|self translated()
 * @method static Builder|self translatedIn(?string $locale = null)
 * @method static Builder|self visible()
 * @method static Builder|self whereCreatedAt($value)
 * @method static Builder|self whereDescription($value)
 * @method static Builder|self whereGroup($value)
 * @method static Builder|self whereId($value)
 * @method static Builder|self whereInGroupPosition($value)
 * @method static Builder|self whereKey($value)
 * @method static Builder|self whereName($value)
 * @method static Builder|self whereStatus($value)
 * @method static Builder|self whereTranslatable($value)
 * @method static Builder|self whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|self whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|self whereType($value)
 * @method static Builder|self whereUpdatedAt($value)
 * @method static Builder|self whereValue($value)
 * @method static Builder|self withTranslation()
 * @method static Builder|self withTranslations()
 */
class AdminConfiguration extends Model implements WithTypesContract
{
    use Translatable, WithTranslationsTrait;
    use VisibleTrait, PositionSortTrait, WithTypes;

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

    protected static array $types = [
        self::type_TITLE => self::type_TITLE,
        self::type_TEXT => self::type_TEXT,
        self::type_IMAGE => self::type_IMAGE,
    ];

    public const type_TITLE = 'title'; // short text /input - type=text
    public const type_TEXT  = 'text';  // long text /textarea
    public const type_IMAGE = 'image'; // image /input - file select

    /**
     * @return mixed|string
     */
    public function getValue()
    {
        return $this->translatable
            ? ($this->type === self::type_IMAGE
                ? self::path($this->translate(app()->getLocale())->content ?? '')
                : $this->translate(app()->getLocale())->content ?? ''
            )
            : $this->value ?? '';
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

        $data = AdminConfiguration::joinTranslations()->visible()
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
            $data[$admin_configuration->group][$admin_configuration->key][] = $admin_configuration->getValue();
        }

        return $data;
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

    private static function path($path): string
    {
        $path = str_replace('/storage', '', $path);
        return asset('/storage'.$path);
    }

    public static function __callStatic($method, $parameters)
    {
        if(\Str::startsWith($method, 'item')){
            $type = strtolower(str_replace('item', '', $method));
            if(in_array($type, self::getTypesKeys())){
                return self::item(
                    $type,
                    Arr::get($parameters, 0),
                    Arr::get($parameters, 1),
                    Arr::get($parameters, 2),
                    Arr::get($parameters, 3)
                );
            }
        }

        return parent::__callStatic($method, $parameters);
    }

}
