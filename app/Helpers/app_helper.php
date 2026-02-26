<?php

if (!function_exists('get_setting')) {
    function get_setting($key, $defaultValue = '') {
        $db = \Config\Database::connect();
        $builder = $db->table('settings');
        $row = $builder->where('setting_key', $key)->get()->getRow();
        return $row ? $row->setting_value : $defaultValue;
    }
}
