<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MenuRole;
use App\Models\Role;
use App\Models\Menu;

class MenuRoleController extends Controller
{
    public function index()
    {
        return MenuRole::with(['role', 'menu'])->get();
    }
    public function indexRole()
    {
        $role =  Role::select('id', 'name')->get();
        return response()->json($role);
    }
    public function store(Request $request)
    {
        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'menu_id' => 'required|exists:menus,id',
        ]);

        $data = MenuRole::create([
            'role_id' => $request->role_id,
            'menu_id' => $request->menu_id,
        ]);

        return response()->json($data);
    }

    public function show($id)
    {
        return MenuRole::with(['role', 'menu'])->findOrFail($id);
    }
    public function menus($roleId)
    {
        $role = Role::with('menus')->findOrFail($roleId);

        $menus = Menu::all()->map(function ($menu) use ($role) {
            return [
                'id' => $menu->id,
                'title' => $menu->title,
                'checked' => $role->menus->contains($menu->id),
            ];
        });

        return response()->json($menus);
    }
    public function update(Request $request, $id)
    {
        $menuRole = MenuRole::findOrFail($id);

        $request->validate([
            'role_id' => 'required|exists:roles,id',
            'menu_id' => 'required|exists:menus,id',
        ]);

        $menuRole->update([
            'role_id' => $request->role_id,
            'menu_id' => $request->menu_id,
        ]);

        return response()->json($menuRole);
    }
    public function saveMenus(Request $request, $id)
    {
        $request->validate([
            'menu_ids' => 'array',
            'menu_ids.*' => 'integer'
        ]);

        $role = Role::findOrFail($id);

        // 🔥 sync menu_role
        $role->menus()->sync($request->menu_ids);

        return response()->json([
            'message' => 'Menu role berhasil disimpan'
        ]);
    }
    public function destroy($id)
    {
        $menuRole = MenuRole::findOrFail($id);
        $menuRole->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
