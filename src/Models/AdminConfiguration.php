<?php

namespace HexideDigital\AdminConfigurations\Models;

use Astrotomic\Translatable\Translatable;
use HexideDigital\HexideAdmin\Contracts\WithTypesContract;
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
 * @property string|null $content
 * @property string|array|null $value
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
 * @method static Builder|static sorted(string $direction = 'ASC')
 * @method static Builder|static sortedAsc()
 * @method static Builder|static sortedDesc()
 * @method static Builder|static translated()
 * @method static Builder|static translatedIn(?string $locale = null)
 * @method static Builder|static visible()
 * @method static Builder|static forGroup($group)
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
    use WithTypes;

    public const DefaultType = self::TEXT;

    /** one-line text @var string */
    public const TEXT = 'text';
    /** multiline text @var string */
    public const TEXTAREA = 'textarea';
    /** multiline text with editor @var string */
    public const EDITOR = 'editor';
    /** day of week @var string */
    public const WEEKDAY = 'weekday';
    /** time @var string */
    public const TIME = 'time';
    /** date @var string */
    public const DATE = 'date';
    /** logic type @var string */
    public const BOOLEAN = 'boolean';
    /** list of items with one selectable @var string */
    public const SELECT = 'select';
    /** list of items with more selectable @var string */
    public const MULTI_SELECT = 'multi_select';
    /** image path @var string */
    public const IMAGE = 'image';
    /** file path @var string */
    public const FILE = 'file';
    /** array of range @var string */
    public const RANGE = 'range';
    /** banner with title, text, image and button @var string */
    public const IMG_BUTTON = 'img_button';

    /** @var array<string> */
    protected static array $types = [
        self::TEXT,
        self::TEXTAREA,
        self::EDITOR,
        self::WEEKDAY,
        self::TIME,
        self::DATE,
        self::BOOLEAN,
        self::SELECT,
        self::MULTI_SELECT,
        self::IMAGE,
        self::FILE,
        self::RANGE,
        self::IMG_BUTTON,
    ];


    public $translationModel = AdminConfigurationTranslation::class;

    protected array $translatedAttributes = ['text', 'json',];

    protected $fillable = [
        'key', 'type', 'name', 'description', 'translatable', 'content', 'value', 'status', 'group', 'in_group_position',
    ];

    protected $casts = [
        'status'       => 'bool',
        'translatable' => 'bool',
        'value'        => 'array',
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

        static::deleted(function (AdminConfiguration $adminConfiguration) {

        });
    }

    public function setKeyAttribute(string $value)
    {
        $this->attributes['key'] = Str::slug($value, '_');
    }

    public function setValueAttribute($value)
    {
        switch ($this->type) {
            case self::MULTI_SELECT:
                $value = json_encode($value ?: []);

                break;

            case self::RANGE:
                $value = array_wrap($value);

                $value = json_encode([
                    'from' => array_get($value, 'from'),
                    'to'   => array_get($value, 'to'),
                ]);

                break;

            case self::IMG_BUTTON:
                $value = array_wrap($value);

                $value = json_encode([
                    'image' => array_get($value, 'image'),
                    'url'   => array_get($value, 'url'),
                ]);

                break;
        };

        $this->attributes['value'] = $value;
    }

    public function getValueAttribute($value)
    {
        if ($this->translatable) {
            /*todo also can be returned json*/
            return $this->translate()->text;
        }

        if ($this->type == self::MULTI_SELECT) {
            if (empty($value)) {
                return [];
            }

            $values = [];

            foreach (json_decode($value, true) as $value) {
                $values[$value] = $value;
            }

            return $values;
        }

        if (in_array($this->type, [self::RANGE, self::IMG_BUTTON])) {
            return json_decode($value, true);
        }

        return $value;
    }

    /**
     * @param Builder $builder
     * @param string|array<string> $groups
     *
     * @return Builder
     */
    public function scopeForGroup(Builder $builder, $groups): Builder
    {
        return $builder->whereIn('group', array_wrap($groups));
    }

    public function scopeSorted(Builder $builder, string $direction = 'ASC'): Builder
    {
        return $builder->orderBy('in_group_position', $direction);
    }

    public function scopeSortedAsc(Builder $builder): Builder
    {
        return $builder->orderBy('in_group_position', 'ASC');
    }

    public function scopeSortedDesc(Builder $builder): Builder
    {
        return $builder->orderBy('in_group_position', 'DESC');
    }

    /**
     * @param string|array $group
     *
     * @return array
     */
    public static function varGroups($group = []): array
    {
        $collection = AdminConfiguration::visible()
            ->joinTranslations()
            ->select([
                'admin_configurations.*',
                'admin_configuration_translations.text as text',
            ])
            ->forGroup($group)
            ->sorted()
            ->get()
            ->groupBy('group');

        $data = [];

        /** @var AdminConfiguration $admin_configuration */
        foreach ($collection as $group => $admin_configurations) {
            $values = [];

            foreach ($admin_configurations as $admin_configuration) {
                $values[$admin_configuration->key] = $admin_configuration->value;
            }

            $data[$group] = $values;
        }

        return $data;

    }

    public static function getValueByKey(string $key)
    {
        $configuration = self::where('key', $key)->first();

        if (!$configuration) {
            $configValue = config('variables.' . $key);

            if (!$configValue) {
                return null;
            }

            $configValue['key'] = $key;

            $data = Arr::only($configValue, ['key', 'name', 'type']);

            $data = isset($configValue['localization'])
                ? array_merge($data, ['multilingual' => true], $configValue['localization'])
                : array_merge($data, ['value' => $configValue['plain_value']]);

            $configuration = self::create($data);

            return $configuration->value;
        }

        return $configuration->value;
    }

    protected static function path($path): string
    {
        $path = str_replace('/storage', '', $path);

        return asset('/storage' . $path);
    }

}
