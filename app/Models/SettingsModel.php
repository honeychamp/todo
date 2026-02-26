<?php

namespace App\Models;

use CodeIgniter\Model;

class SettingsModel extends Model
{
    protected $table         = 'settings';
    protected $primaryKey    = 'setting_key';
    protected $allowedFields = ['setting_key', 'setting_value'];
    protected $useTimestamps = false;

    public function getSetting($key, $default = '')
    {
        $setting = $this->where('setting_key', $key)->first();
        return $setting ? $setting['setting_value'] : $default;
    }

    public function getAllSettings()
    {
        $settings = $this->findAll();
        $formatted = [];
        foreach ($settings as $s) {
            $formatted[$s['setting_key']] = $s['setting_value'];
        }
        return $formatted;
    }
}
