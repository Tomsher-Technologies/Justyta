@component('mail::message')
# Hello {{ $vendorName }},

Your law firm account status has been "{{ ucfirst($status) }}" by our admin team.

@if($status === 'approved')
    You can now log in and start using your account using the owner credentials provided during registration.
@else
    Please contact our support team for more information about your account status.
@endif

Thanks,<br>
Team {{ env('APP_NAME') }}
@endcomponent
