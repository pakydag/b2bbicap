<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $user = auth()->user();
        if ($user) {
            $shop_enabled = \App\Models\Setting::where('key', 'shop_enabled')->value('value') == '1';
            if ($shop_enabled && ($user->is_super_admin || $user->can_manage_shop)) {
                return redirect()->route('admin.shop.dashboard');
            }
            if (!$user->is_super_admin && $user->can_manage_agents && !$user->can_manage_site) {
                return redirect()->route('admin.b2b.dashboard');
            }
        }
        return view('admin.dashboard');
    }
}
