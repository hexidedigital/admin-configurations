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
 * @method static Builder|static joinTranslations(?string $modelTable = null, ?string $translationsTable = null, ?string $modelTableKey = null, ?string $translationsTableKey = null)
 * @method static Builder|static listsTranslations(string $translationField)
 * @method static Builder|static newModelQuery()
 * @method static Builder|static newQuery()
 * @method static Builder|static notTranslatedIn(?string $locale = null)
 * @method static Builder|static orWhereTranslation(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static orWhereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static orderByTranslation(string $translationField, string $sortMethod = 'asc')
 * @method static Builder|static query()
 * @method static Builder|static sorted(string $direction = 'ASC', string $field = 'position')
 * @method static Builder|static translated()
 * @method static Builder|static translatedIn(?string $locale = null)
 * @method static Builder|static visible()
 * @method static Builder|static whereCreatedAt($value)
 * @method static Builder|static whereDescription($value)
 * @method static Builder|static whereGroup($value)
 * @method static Builder|static whereId($value)
 * @method static Builder|static whereInGroupPosition($value)
 * @method static Builder|static whereKey($value)
 * @method static Builder|static whereName($value)
 * @method static Builder|static whereStatus($value)
 * @method static Builder|static whereTranslatable($value)
 * @method static Builder|static whereTranslation(string $translationField, $value, ?string $locale = null, string $method = 'whereHas', string $operator = '=')
 * @method static Builder|static whereTranslationLike(string $translationField, $value, ?string $locale = null)
 * @method static Builder|static whereType($value)
 * @method static Builder|static whereUpdatedAt($value)
 * @method static Builder|static whereValue($value)
 * @method static Builder|static withTranslation()
 * @method static Builder|static withTranslations()
 */
class AdminConfiguration extends Model implements WithTypesContract
{
    use Translatable, WithTranslationsTrait;
    use VisibleTrait, PositionSortTrait, WithTypes;

    /**
     * @var string[]
     */
    protected $translatedAttributes = [
        'content'
    ];

    /**
     * @var string[]
     */
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

    /**
     * @var string[]
     */
    protected static $types = [
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
            ? ($this->type === static::type_IMAGE
                ? static::path($this->content ?? '')
                : $this->content ?? ''
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

        $data = AdminConfiguration::visible()
            ->joinTranslations()
            ->select([
                'admin_configurations.*',
                'admin_configuration_translations.content as content',
            ])
            ->whereIn('group', $name)
            ->orderBy('in_group_position')
            ->get();

        return static::makeVariablesMap($data);
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

    protected static function path($path): string
    {
        $path = str_replace('/storage', '', $path);
        return asset('/storage'.$path);
    }

    protected static function createItem($method, $parameters){
        $type = strtolower(str_replace('item', '', $method));
        if(in_array($type, static::getTypesKeys())){
            return static::item(
                $type,
                Arr::get($parameters, 0),
                Arr::get($parameters, 1),
                Arr::get($parameters, 2),
                Arr::get($parameters, 3)
            );
        }
    }

    public static function __callStatic($method, $parameters)
    {
        if(\Str::startsWith($method, 'item')){
            static::createItem($method, $parameters);
        }

        return parent::__callStatic($method, $parameters);
    }

}
