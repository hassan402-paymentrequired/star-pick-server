<x-mail::message>
    # Welcome to {{ config('app.name') }} 🎉

    Hi {{ $user->name ?? 'there' }},

    We're excited to have you join us at **{{ config('app.name') }}**!

    Your One-Time Password (OTP) is **{{ $otp }}**. Please use this code to complete your registration.

    If you have any questions or need help getting started, feel free to reach out — we're always here to help.

    Thanks for joining us,<br>
    The {{ config('app.name') }} Team
</x-mail::message>

