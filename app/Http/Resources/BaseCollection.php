<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Support\Str;

class BaseCollection extends ResourceCollection
{
    public function toArray($request)
    {
        return [
            'data' => $this->collection,
        ];
    }

    public function with($request)
    {
        $modelName = $this->getModelName($request);
        $action = $this->getAction($request);

        return  [
            'message' => "{$modelName} {$action} successfully."
        ];
    }

    protected function getModelName($request)
    {
        $routeName = $request->route()->getName();
        return Str::of($routeName)->before('.')->singular()->title();
    }

    protected function getAction($request)
    {
        $action = $request->route()->getActionMethod();

        return match ($action) {
            'index' => 'listed',
            default => 'processed',
        };
    }
}
