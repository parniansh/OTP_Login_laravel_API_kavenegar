<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Route;

class ErrorResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return [
            'path' => Route::getFacadeRoot()->current()->uri(),
            'method' => request()->method(),
            'error' => isset($this->error) ? $this->error : 'Error not set !',
            'message' => isset($this->message) ? $this->message : 'Message not set !',
            'timestamp' => Carbon::now()->timestamp,
            'status' => (int) isset($this->status) ? $this->status : 422,
            'debug' => isset($this->debug) ? $this->debug : (object)[]
        ];
    }
    public function withResponse($request,$response){
        $response->setStatusCode(422);
    }
}
