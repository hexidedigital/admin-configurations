<?php

namespace HexideDigital\AdminConfigurations\Models;

use Astrotomic\Translatable\Translatable;
use HexideDigital\HexideAdmin\Contracts\WithTypesContract;
use HexideDigital\HexideAdmin\Models\Traits\PositionSortTrait;
use HexideDigital\HexideAdmin\Models\Traits\VisibleTrait;
use HexideDigital\HexideAdmin\Models\Traits\WithTranslationsTrait;
use HexideDigital\HexideAdmin\Models\Traits\WithTypes;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

/**
 * @method static array itemText(string $key, string $name, ?bool $translatable = null, $value = null)
 * @method static array itemTitle(string $key, string $name, ?bool $translatable = null, $value = null)
 * @method static array itemImage(string $key, string $name, ?bool $translatable = null, $value = null)
 *
 * @mixin AdminConfigurationTranslation
 * @mixin \Eloquent
 *
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
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read AdminConfigurationTranslation|null $translation
 * @property-read Collection|AdminConfigurationTranslation[] $translations
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
    use VisibleTrait;
    use PositionSortTrait;
    use WithTypes;

    public const DefaultType = self::TEXT;

    /** simple one-line text @var string */
    public const TEXT = 'text';
    /** multiline text @var string */
    public const TEXTAREA = 'textarea';
    /** multiline text with editor @var string */
    public const EDITOR = 'editor';
    /** - @var string */
    public const WEEKDAY = 'weekday';
    /** - @var string */
    public const TIME = 'time';
    /** checkbox type @var string */
    public const BOOLEAN = 'boolean';
    /** list of items with one selectable @var string */
    public const SELECT = 'select';
    /** list of items with more selectable @var string */
    public const MULTI_SELECT = 'multi_select';
    /** - @var string */
    public const IMAGE = 'image';
    /** - @var string */
    public const FILE = 'file';
    /** - @var string */
    public const RANGE = 'range';
    /** - @var string */
    public const IMG_BUTTON = 'img_button';
    /** - @var string */
    public const DATE = 'date';

    /** - @var string */
    public const COMMISSION_TYPE = 'commission_type';

    /** @var array<string> */
    protected static array $types = [
        self::TEXT,
        self::TEXTAREA,
        self::EDITOR,
        self::WEEKDAY,
        self::TIME,
        self::BOOLEAN,
        self::SELECT,
        self::MULTI_SELECT,
        self::IMAGE,
        self::FILE,
        self::RANGE,
        self::IMG_BUTTON,
        self::DATE,
        self::COMMISSION_TYPE,
    ];


    public $translationModel = AdminConfigurationTranslation::class;

    protected array $translatedAttributes = [
        'text',
        'json',
    ];

    protected $fillable = [
        'key',
        'type',
        'name',
        'description',
        'translatable',
        'content',
        'value',
        'status',
        'group',
        'in_group_position',
    ];

    protected $casts = [
        'status'       => 'bool',
        'translatable' => 'bool',
        'value'        => 'json',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (AdminConfiguration $adminConfiguration) {
            $adminConfiguration->key = $adminConfiguration->attributes['key'];
        });

        static::updating(function (AdminConfiguration $adminConfiguration) {
            $adminConfiguration->key = $adminConfiguration->attributes['key'];
        });
    }

    public function setKeyAttribute($value)
    {
        $this->attributes['key'] = Str::replace('-', '_', Str::slug($value));
    }

    public function getValue(): string
    {
        return $this->translatable
            ? ($this->type === static::IMAGE
                ? static::path($this->content ?? '')
                : $this->content ?? ''
            )
            : $this->value ?? '';
    }

    /**
     * @param string|array $name
     *
     * @return array
     */
    public static function var_groups($name = []): array
    {
        return static::makeVariablesMap(
            AdminConfiguration::visible()
                ->joinTranslations()
                ->select([
                    'admin_configurations.*',
                    'admin_configuration_translations.text as text',
                ])
                ->whereIn('group', array_wrap($name))
                ->orderBy('in_group_position')
                ->get()
                ->groupBy('group')
        );
    }

    public static function makeVariablesMap(Collection $collection): array
    {
        $data = [];

        /** @var AdminConfiguration $admin_configuration */
        foreach ($collection as $group => $admin_configurations) {
            $values = [];

            foreach ($admin_configurations as $admin_configuration) {
                $values[$admin_configuration->key][] = $admin_configuration->getValue();
            }

            $data[$group] = $values;
        }

        return $data;
    }

    public static function item(string $type, string $key, string $name, ?bool $translatable = null, $value = null): array
    {
        $item = [
            'key'  => $key,
            'type' => $type,
            'name' => $name,
        ];

        if ($translatable) {
            $item['translatable'] = true;

            if (is_array($value)) {
                foreach (config('app.locales') as $locale) {
                    $item[$locale] = ['text' => $value[$locale] ?? ''];
                }
            } else {
                foreach (config('app.locales') as $locale) {
                    $item[$locale] = ['text' => $value ?? ''];
                }
            }

        } else if (!empty($value)) {
            $item['content'] = $value;
        }

        return $item;
    }

    protected static function path($path): string
    {
        $path = str_replace('/storage', '', $path);

        return asset('/storage' . $path);
    }

    protected static function createItem($method, $parameters): ?array
    {
        $type = strtolower(str_replace('item', '', $method));
        if (in_array($type, static::getTypesKeys())) {
            return static::item(
                $type,
                Arr::get($parameters, 0),
                Arr::get($parameters, 1),
                Arr::get($parameters, 2),
                Arr::get($parameters, 3)
            );
        }

        return null;
    }

    public static function __callStatic($method, $parameters)
    {
        if (Str::startsWith($method, 'item')) {
            return static::createItem($method, $parameters);
        }

        return parent::__callStatic($method, $parameters);
    }
}
