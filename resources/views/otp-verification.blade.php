<x-mail::message>
# Hi {{ $user->name ?? 'there' }}

    $this is your One-Time Password (OTP): **{{ $otp }}**

    If you have any questions or need help getting started, feel free to reach out — we're always here to help.

    <x-mail::button :url="''">
        Button Text
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
