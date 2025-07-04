<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;


class UserController extends Controller
{

    public function account(Request $request){
        $user = $request->user();

        return response()->json([
            'status' => true,
            'message' => 'Success',
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'user_type' => $user->user_type,
                'phone' => $user->phone,
                'language' => $user->language,
            ],
        ], 200);
    }

    public function editProfile(Request $request){
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'full_name'     => 'required|string|max:255',
            'phone'    => 'nullable|string|max:20',
            'language' => 'nullable|string|in:en,ar,fr,fa,ru,zh', 
        ],[
            'full_name.required' => __('messages.full_name_required'),
        ]);
        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }

        $validated = [
            'name' => $request->full_name ?? $user->name,
            'phone' => $request->phone ?? $user->phone,
            'language' => $request->language ?? $user->language
        ];

        // Update fields
        $user->update($validated);

        return response()->json([
            'status' => true,
            'message' => __('messages.profile_updation_success'),
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'phone'    => $user->phone,
                'language' => $user->language,
                'email'    => $user->email,
            ]
        ]);
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'new_password' => [
                'required',
                'string',
                'min:6',
                'confirmed'           // must match new_password_confirmation
            ],
        ],[
            'current_password.required'    => __('messages.current_password_required'),
            'new_password.required'        => __('messages.new_password_required'),
            'new_password.min'             => __('messages.new_password_min'),
            'new_password.confirmed'       => __('messages.new_password_confirmed'),
        ]);

        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }

        $user = $request->user();

        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json([
                'status' => false,
                'message' => __('messages.current_password_incorrect'),
            ], 200);
        }

        $user->password = Hash::make($request->new_password);
        $user->save();

        return response()->json([
            'status' => true,
            'message' => __('messages.password_changed_successfully'),
        ], 200);
    }

    public function deleteAccount(Request $request)
    {
        $user = $request->user();

        // Soft delete the user
        $user->delete();

        // Optionally revoke tokens (if using Sanctum)
        $user->tokens()->delete();

        return response()->json([
            'status' => true,
            'message' => __('messages.account_deleted_successfully')
        ], 200);
    }

    public function getGroupedUserNotifications(Request $request)
    {
        $lang       = $request->header('lang') ?? env('APP_LOCALE','en'); // default to English 
        $user       = $request->user();

        $today      = Carbon::today();
        $yesterday  = Carbon::yesterday();

        $services   = \App\Models\Service::with('translations')->get();

        $serviceMap = [];

        foreach ($services as $service) {
            foreach ($service->translations as $translation) {
                $serviceMap[$service->slug][$translation->lang] = $translation->title;
            }
        }
    
        $allNotifications = $user->notifications();

        // Today
        $todayNotifications = (clone $allNotifications)
            ->whereDate('created_at', $today)
            ->orderByDesc('created_at')
            ->get()
            ->map(function ($notification) use($lang, $serviceMap) {
                $data = $notification->data;
                $slug = $data['service'] ?? null;

                $serviceName =  $slug && isset($serviceMap[$slug]) ? ($serviceMap[$slug][$lang] ?? $serviceMap[$slug][env('APP_LOCALE','en')] ?? $slug) : '';

                return [
                    'id'        => $notification->id,
                    'message'   => __($notification->data['message'], [
                                        'service'   => $serviceName,
                                        'reference' => $data['reference_code'],
                                    ]),
                    'time'      => $notification->created_at->format('h:i A'), // or 'h:i A' for AM/PM
                ];
            });
        // Yesterday
        $yesterdayNotifications = (clone $allNotifications)
                            ->whereDate('created_at', $yesterday)
                            ->orderByDesc('created_at')
                            ->get()
                            ->map(function ($notification) use($lang, $serviceMap) {
                                $data = $notification->data;
                                $slug = $data['service'] ?? null;

                                $serviceName =  $slug && isset($serviceMap[$slug]) ? ($serviceMap[$slug][$lang] ?? $serviceMap[$slug][env('APP_LOCALE','en')] ?? $slug) : '';

                                return [
                                    'id'        => $notification->id,
                                    'message'   => __($notification->data['message'], [
                                                        'service'   => $serviceName,
                                                        'reference' => $data['reference_code'],
                                                    ]),
                                    'time'      => $notification->created_at->format('h:i A'), // or 'h:i A' for AM/PM
                                ];
                            });

        $paginatedPast = (clone $allNotifications)
                        ->whereDate('created_at', '<', $yesterday)
                        ->orderByDesc('created_at')
                        ->paginate(1);

        $pastNotifications = collect($paginatedPast->items())
                ->map(function ($notification) use($lang, $serviceMap) {
                    $data = $notification->data;
                    $slug = $data['service'] ?? null;

                    $serviceName =  $slug && isset($serviceMap[$slug]) ? ($serviceMap[$slug][$lang] ?? $serviceMap[$slug][env('APP_LOCALE','en')] ?? $slug) : '';

                    return [
                        'message'   => __($notification->data['message'], [
                                            'service'   => $serviceName,
                                            'reference' => $data['reference_code'],
                                        ]),
                        'time'      => $notification->created_at->format('h:i A'), // or 'h:i A' for AM/PM
                    ];
                });

        // 🔹 Merge all current notification IDs
        $allShownIds = collect($todayNotifications)
                        ->merge($yesterdayNotifications)
                        ->merge($paginatedPast->items())
                        ->pluck('id');

        // 🔹 Mark only these as read
        $user->unreadNotifications()
            ->whereIn('id', $allShownIds)
            ->update(['read_at' => now()]);
        
        return response()->json([
            'status'    => true,
            'message'   => 'Success',
            'data'      => [
                'today'         => $todayNotifications,
                'yesterday'     => $yesterdayNotifications,
                'past'          => $pastNotifications,
                'current_page'  => $paginatedPast->currentPage(),
                'last_page'     => $paginatedPast->lastPage(),
                'per_page'      => $paginatedPast->perPage(),
                'total'         => $paginatedPast->total(),
            ],
        ]);
    }

    public function clearAllNotifications(Request $request)
    {
        $user = $request->user();

        // Delete all notifications
        $user->notifications()->delete();

        return response()->json([
            'status'    => true,
            'message'   => __('messages.notifications_cleared_successfully')
        ]);
    }

    public function getUnreadNotificationCount(Request $request)
    {
        $user = $request->user();

        $count = $user->unreadNotifications()->count();

        return response()->json([
            'status'    => true,
            'message'   => 'success',
            'data'      => $count,
        ]);
    }

    public function changeLanguage(Request $request){
        $user   = $request->user();

        $validator = Validator::make($request->all(), [
            'language' => 'required|string|in:en,ar,fr,fa,ru,zh', 
        ],[
            'language.required' => __('messages.language_required'),
            'language.string' => __('messages.language_string'),
            'language.in' => __('messages.language_in'),
        ]);
        if ($validator->fails()) {
            $message = implode(' ', $validator->errors()->all());

            return response()->json([
                'status' => false,
                'message' => $message,
            ], 200);
        }

        $validated = [
            'language' => $request->language ?? $user->language
        ];

        // Update fields
        $user->update($validated);

        return response()->json([
            'status' => true,
            'message' => __('messages.language_updation_success'),
            'data'    => [
                'id'       => $user->id,
                'name'     => $user->name,
                'phone'    => $user->phone,
                'language' => $user->language,
                'email'    => $user->email,
            ]
        ]);
    }
}
