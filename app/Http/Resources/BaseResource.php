<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Str;

class BaseResource extends JsonResource
{
    public function toArray($request)
    {
        return ["data" => $this->resource->toArray()];
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
            'store' => 'created',
            'update' => 'updated',
            'destroy' => 'deleted',
            default => 'processed',
        };
    }
}
