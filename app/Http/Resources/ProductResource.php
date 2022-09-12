<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class ProductResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function stockStatus($count){
        $status = "";
        if($count>10){
            $status = "available";
        }elseif($count>0){
            $status = "few";
        }elseif($count===0){
            $status = "No stack";
        }
        return $status;
    }
    public function toArray($request)
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "price" => $this->price,
            "show-price" => $this->price."mmk",
            "stock" => $this->stock,
            "stock-status" => $this->stockStatus($this->stock),
            "date" => $this->created_at->format('Y d m'),
            "time" => $this->created_at->format('g:i A'),
            "owner" => new UserResource($this->user),
            "photos" => PhotoResource::collection($this->photos),
        ];
    }
}
