<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    protected $userPermissions;
    public function index(Request $request)
    {
        $permissions = $request->user()->getAllPermissions()->pluck(['id']);

        $this->userPermissions = Permission::whereIn('id', $permissions)
            ->with(['module'])
            ->get()
            ->pluck(['module']);

        $menuLinks = $this->getParentLinks();

        $secondLevelLinks = $this->getChildrenLinks();

        $loopCounter = 0;
        while ($loopCounter <= count($menuLinks) - 1) {
            $menuIndex = $menuLinks[$loopCounter]['id'];
            if (isset($secondLevelLinks[$menuIndex])) {
                $menuLinks[$loopCounter]['items'] = $secondLevelLinks[$menuIndex];
            }
            $loopCounter++;
        }
        return response()->json(['data' => $menuLinks]);
    }

    protected function getParentLinks()
    {
        $parentLinks = $this->userPermissions->map(function ($item, $key) {
            if (isset($item->parent)) {
                $linkMeta = $item->parent->only(['id', 'weight', 'display_name', 'icon_class']);
                return [
                    'id' => $linkMeta['id'],
                    'weight' => $linkMeta['weight'],
                    'title' => $linkMeta['display_name'],
                    'action' => $linkMeta['icon_class'],
                    'icon_class' => $linkMeta['icon_class'],
                    'items' => []
                ];
            }

            return [
                'id' => $item['id'],
                'weight' => $item['weight'],
                'display_name' => $item['display_name'],
                'icon_class' => $item['icon_class'],
                'route' => $item['route']
            ];
        });

        return $parentLinks->unique()->sortBy('weight')->values()->all();
    }

    protected function getChildrenLinks()
    {
        $navigationLinks = $this->userPermissions->map(function ($item, $key) {
            return $item->only(['weight', 'parent_id', 'display_name', 'icon_class', 'route']);
        });

        return $navigationLinks
            ->unique()
            ->sortBy('weight')
            ->values()
            ->groupBy('parent_id')
            ->toArray();
    }
}
