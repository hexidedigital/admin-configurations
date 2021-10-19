<?php

namespace HexideDigital\AdminConfigurations\Classes;

use HexideDigital\AdminConfigurations\Models\AdminConfiguration;
use Illuminate\Database\Eloquent\Collection;

class Configuration
{

    public const type_TITLE = 'title'; // short text /input - type=text
    public const type_TEXT  = 'text';  // long text /textarea
    public const type_IMAGE = 'image'; // image /input - file select

    public const types = [
        self::type_TITLE,
        self::type_TEXT,
        self::type_IMAGE,
    ];

    public function getTypes(): array
    {
        return self::types;
    }

    public function item(string $type, string $key, string $name, ?bool $translatable = null, $value = null): array
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

    public function textItem(string $key, string $name, ?bool $translatable = null, $value = null): array
    {
        return self::item(self::type_TEXT, $key, $name, $translatable, $value);
    }
    public function titleItem(string $key, string $name, ?bool $translatable = null, $value = null): array
    {
        return self::item(self::type_TITLE, $key, $name, $translatable, $value);
    }
    public function imageItem(string $key, string $name, ?bool $translatable = null, $value = null): array
    {
        return self::item(self::type_IMAGE, $key, $name, $translatable, $value);
    }


    /**
     * @param string|array $name
     * @return array
     */
    public function var_groups($name = []): array
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

    public function makeVariablesMap(Collection $array): array
    {
        $data = [];
        /** @var AdminConfiguration $variable */
        foreach ($array as $variable) {
            $value = $variable->translatable
                ? ($variable->type === self::type_IMAGE
                    ? asset($variable->translate(app()->getLocale())->content??'')
                    : $variable->translate(app()->getLocale())->content ?? ''
                )
                : $variable->value ?? '';

            $data[$variable->group][$variable->key][] = $value;
        }
        return $data;
    }

}
