<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class SuccessResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public static $wrap = 'data';
    public function toArray($request)
    {
        return isset($this->data) ? $this->data : [];
    }
    public function withResponse($request,$response){
        $response->setStatusCode(200);
    }
}
