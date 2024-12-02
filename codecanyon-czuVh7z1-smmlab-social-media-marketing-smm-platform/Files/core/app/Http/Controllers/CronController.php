<?php

namespace App\Http\Controllers;

use App\Constants\Status;
use App\Lib\CurlRequest;
use App\Models\CronJob;
use App\Models\CronJobLog;
use App\Models\Order;
use App\Models\Transaction;
use Carbon\Carbon;

class CronController extends Controller {
    public function cron() {
        $general            = gs();
        $general->last_cron = now();
        $general->save();

        $crons = CronJob::with('schedule');

        if (request()->alias) {
            $crons->where('alias', request()->alias);
        } else {
            $crons->where('next_run', '<', now())->where('is_running', Status::YES);
        }
        $crons = $crons->get();
        foreach ($crons as $cron) {
            $cronLog              = new CronJobLog();
            $cronLog->cron_job_id = $cron->id;
            $cronLog->start_at    = now();
            if ($cron->is_default) {
                $controller = new $cron->action[0];
                try {
                    $method = $cron->action[1];
                    $controller->$method();
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            } else {
                try {
                    CurlRequest::curlContent($cron->url);
                } catch (\Exception $e) {
                    $cronLog->error = $e->getMessage();
                }
            }
            $cron->last_run = now();
            $cron->next_run = now()->addSeconds($cron->schedule->interval);
            $cron->save();

            $cronLog->end_at = $cron->last_run;

            $startTime         = Carbon::parse($cronLog->start_at);
            $endTime           = Carbon::parse($cronLog->end_at);
            $diffInSeconds     = $startTime->diffInSeconds($endTime);
            $cronLog->duration = $diffInSeconds;
            $cronLog->save();
        }
        if (request()->target == 'all') {
            $notify[] = ['success', 'Cron executed successfully'];
            return back()->withNotify($notify);
        }
        if (request()->alias) {
            $notify[] = ['success', keyToTitle(request()->alias) . ' executed successfully'];
            return back()->withNotify($notify);
        }
    }

    public function placeOrderToApi() {
        $apiOrders          = Order::pending()->with('provider')->where('api_provider_id', '!=', Status::API_ORDER_NOT_PLACE)->where('order_placed_to_api', Status::API_ORDER_NOT_PLACE)->get();
        $general            = gs();
        $general->last_cron = now();
        $general->save();
        foreach ($apiOrders as $order) {
            $response = CurlRequest::curlPostContent($order->provider->api_url, [
                'key'      => $order->provider->api_key,
                'action'   => "add",
                'service'  => $order->api_service_id,
                'link'     => $order->link,
                'quantity' => $order->quantity,
            ]);
            $response = json_decode($response);
            if (@$response->error) {
                echo response()->json(['error' => @$response->error]) . '<br>';
                continue;
            }
            //Order placed
            $order->status              = Status::ORDER_PROCESSING;
            $order->order_placed_to_api = 1;
            $order->api_order_id        = $response->order;
            $order->save();
        }
    }
    public function serviceUpdate() {
        $orders             = Order::processing()->with('provider')->where('api_provider_id', '!=', 0)->where('order_placed_to_api', Status::YES)->get();
        $general            = gs();
        $general->last_cron = now();
        $general->save();
        foreach ($orders as $order) {
            $response = CurlRequest::curlPostContent($order->provider->api_url, [
                'key'    => $order->provider->api_key,
                'action' => "status",
                'order'  => $order->api_order_id,
            ]);
            $response = json_decode($response);
            if (@$response->error) {
                echo response()->json(['error' => @$response->error]) . '<br>';
                continue;
            }
            $order->start_counter = $response->start_count;
            $order->remain        = $response->remains;
            $user                 = $order->user;
            if ($response->status == 'Completed') {
                $order->status = Status::ORDER_COMPLETED;
                $order->save();
                //Send email to user
                notify($user, 'COMPLETED_ORDER', [
                    'service_name' => $order->service->name,
                    'username'     => $order->user->username,
                    'price'        => $order->price,
                    'full_name'    => $order->user->fullname,
                    'category'     => $order->category->name,
                ]);
            }
            if ($response->status == 'Canceled') {
                $order->status = Status::ORDER_CANCELLED;
                $order->save();
                //Send email to user
                notify($user, 'CANCELLED_ORDER', [
                    'service_name' => $order->service->name,
                    'username'     => $order->user->username,
                    'full_name'    => $order->user->fullname,
                    'price'        => $order->price,
                    'category'     => $order->category->name,
                ]);
            }
            if ($response->status == 'Refunded') {
                if ($order->status == Status::ORDER_COMPLETED || $order->status == Status::ORDER_CANCELLED) {
                    $notify[] = ['error', 'This order is not refundable'];
                    return back()->withNotify($notify);
                }
                $order->status = Status::ORDER_REFUNDED;
                $order->save();
                //Refund balance
                $user->balance += $order->price;
                $user->save();
                //Create Transaction
                $transaction               = new Transaction();
                $transaction->user_id      = $user->id;
                $transaction->amount       = $order->price;
                $transaction->post_balance = $user->balance;
                $transaction->trx_type     = '+';
                $transaction->remark       = "refund_order";
                $transaction->details      = 'Refund for Order ' . $order->service->name;
                $transaction->trx          = getTrx();
                $transaction->save();
                //Send email to user
                notify($user, 'REFUNDED_ORDER', [
                    'service_name' => $order->service->name,
                    'price'        => getAmount($order->price),
                    'currency'     => gs()->cur_text,
                    'post_balance' => getAmount($user->balance),
                    'trx'          => $transaction->trx,
                ]);
            }
            $order->save();
        }
    }

}
