<?php

namespace App\Controllers;

use App\Controllers\BaseController;
use App\Models\SettingsModel;

class Settings extends BaseController
{
    public function index()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new SettingsModel();
        $data['settings'] = $model->getAllSettings();

        return view('settings/index', $data);
    }

    public function update()
    {
        if (!session()->get('logged_in')) return redirect()->to(base_url('auth/login'));

        $model = new SettingsModel();
        $posts = $this->request->getPost();

        foreach ($posts as $key => $value) {
            // Upsert: Try to update first, if not exists then insert
            $existing = $model->where('setting_key', $key)->first();
            if ($existing) {
                // Update based on setting_key
                $model->where('setting_key', $key)->set(['setting_value' => $value])->update();
            } else {
                $model->insert(['setting_key' => $key, 'setting_value' => $value]);
            }
        }

        return redirect()->to(base_url('settings'))->with('success', 'System settings updated successfully');
    }
}
