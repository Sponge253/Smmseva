<?php

namespace App\Models;

use App\Traits\GlobalStatus;

use Illuminate\Database\Eloquent\Model;

class Service extends Model
{
	use GlobalStatus;

	public function category()
	{
		return $this->belongsTo(Category::class)->withDefault();
	}

	public function provider()
	{
		return $this->belongsTo(ApiProvider::class, 'api_provider_id', 'id');
	}

	public function favorite()
	{
		return $this->hasMany(Favorite::class, 'service_id');
	}

	public function orders()
	{
		return $this->hasMany(Order::class);
	}
}
