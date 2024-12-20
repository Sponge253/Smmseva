<?php

namespace App\Http\Controllers\User;

use App\Constants\Status;
use App\Http\Controllers\Controller;
use App\Models\AdminNotification;
use App\Models\Category;
use App\Models\Order;
use App\Models\Service;
use App\Models\Transaction;
use Illuminate\Http\Request;

class DripfeedController extends Controller
{
	public function dripfeedOverview()
	{
		$pageTitle = "Dripfeed Order";
		$categories = Category::active()->whereHas('services', function ($query) {
			return $query->active()->where('dripfeed', Status::YES);
		})->with(['services' => function ($query) {
			$query->active()->where('dripfeed', Status::YES);
		}])
			->withCount(['services' => function ($query) {
				$query->active()->where('dripfeed', Status::YES);
			}])->orderBy('name')->get()->map(function ($category) {
				//$minService = $category->services->sortBy(['price_per_k', 'asc'], ['min', 'asc'])->first();
                $minService = $category->services()
                    ->orderBy('price_per_k', 'asc')
                    ->orderBy('min', 'asc')
                    ->first();
				$category->service_min_start = $minService->min;
				$category->price_per_k = $minService->price_per_k;
				return $category;
			});
		return view('Template::user.dripfeed.overview', compact('pageTitle', 'categories'));
	}

	public function dripfeed(Request $request, $serviceId)
	{

		$user    = auth()->user();
		$service = Service::with('category')->active()->where('dripfeed', Status::YES)->findOrFail($serviceId);

		$request->validate([
			'link'      => 'required|url',
			'quantity'  => 'required|integer|gte:' . $service->min . '|lte:' . $service->max,
			'runs'      => 'required|integer|gt:0',
			'intervals' => 'required|integer|gt:0',
		]);
		$price = ($service->price_per_k / 1000) * $request->quantity;

		if ($user->balance < $price) {
			$notify[] = ["error", 'Account does not have sufficient balance, please deposit and try again.'];
			return to_route('user.deposit.index')->withNotify($notify);
		}

		$user->balance -= $price;
		$user->save();

		//Create Transaction
		$transaction               = new Transaction();
		$transaction->user_id      = $user->id;
		$transaction->amount       = $price;
		$transaction->post_balance = $user->balance;
		$transaction->trx_type     = '-';
		$transaction->details      = 'Order for ' . $service->name;
		$transaction->trx          = getTrx();
		$transaction->remark       = 'order';
		$transaction->save();

		//Make order
		$order                  = new Order();
		$order->user_id         = $user->id;
		$order->category_id     = $service->category->id;
		$order->service_id      = $serviceId;
		$order->api_service_id  = $service->api_service_id ?? Status::NO;
		$order->api_provider_id = $service->api_provider_id ?? Status::NO;
		$order->link            = $request->link;
		$order->quantity        = $request->quantity;
		$order->price           = $price;
		$order->start_counter   = $request->quantity / $request->runs;
		$order->runs            = $request->runs;
		$order->interval        = $request->intervals;
		$order->remain          = $request->quantity;
		$order->api_order       = $service->api_service_id ? Status::YES : Status::NO;
		$order->save();

		//Create admin notification
		$adminNotification            = new AdminNotification();
		$adminNotification->user_id   = $user->id;
		$adminNotification->title     = 'New dripfeed order request for ' . $service->name;
		$adminNotification->click_url = urlPath('admin.orders.details', $order->id);
		$adminNotification->save();

		notify($user, 'PENDING_ORDER', [
			'service_name' => $service->name,
			'category'     => $service->category->name,
			'username'     =>  $user->username,
			'full_name'     => $user->fullname,
			'price'        => $price,
			'post_balance' => getAmount($user->balance),
		]);

		$notify[] = ['success', 'Successfully placed your dripfeed order!'];
		return to_route('user.order.details', $order->id)->withNotify($notify);
	}

	public function history()
	{
		$pageTitle   = 'Dripfeed History';
		$orders      = $this->orderData();
		$categories  = $this->categoryData();
		return view('Template::user.orders.history', compact('pageTitle', 'orders', 'categories'));
	}

	public function pending()
	{
		$pageTitle  = "Pending Orders";
		$orders     = $this->orderData('pending');
		$categories = $this->categoryData();
		return view('Template::user.orders.history', compact('pageTitle', 'orders', 'categories'));
	}

	public function processing()
	{
		$pageTitle  = "Processing Orders";
		$orders     = $this->orderData('processing');
		$categories = $this->categoryData();
		return view('Template::user.orders.history', compact('pageTitle', 'orders', 'categories'));
	}

	public function completed()
	{
		$pageTitle  = "Completed Orders";
		$orders     = $this->orderData('completed');
		$categories = $this->categoryData();
		return view('Template::user.orders.history', compact('pageTitle', 'orders', 'categories'));
	}

	public function cancelled()
	{
		$pageTitle  = "Cancelled Orders";
		$orders     = $this->orderData('cancelled');
		$categories = $this->categoryData();
		return view('Template::user.orders.history', compact('pageTitle', 'orders', 'categories'));
	}

	public function refunded()
	{
		$pageTitle  = "Refunded Orders";
		$orders     = $this->orderData('refunded');
		$categories = $this->categoryData();
		return view('Template::user.orders.history', compact('pageTitle', 'orders', 'categories'));
	}

	protected function categoryData()
	{
		$order       = Order::where('user_id', auth()->id())->get();
		$categoryIds = $order->pluck('category_id')->unique();
		return	Category::active()
			->whereIn('id', $categoryIds)
			->whereHas('services', function ($query) {
				$query->active();
			})
			->orderBy('name')
			->get();
	}

	protected function orderData($scope = null)
	{
		if ($scope) {
			$orders = Order::$scope();
		} else {
			$orders = Order::query();
		}
		return $orders->where('user_id', auth()->id())->dripfeedOrder()->searchable(['id', 'service:name'])->filter(['category_id'])->dateFilter()->with(['category', 'user', 'service'])->orderBy('id', 'desc')->paginate(getPaginate());
	}
}
