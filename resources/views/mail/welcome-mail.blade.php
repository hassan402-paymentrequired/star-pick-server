<x-mail::message>
    # Welcome to {{ config('app.name') }} 🎉

    Hi {{ $user->name ?? 'there' }},

    We're excited to have you join us at **{{ config('app.name') }}**!

    You're now part of a community where every move counts — whether you're building your dream team, tracking stats, or
    competing to win. Get started now and make your mark.

    If you have any questions or need help getting started, feel free to reach out — we're always here to help.

    Thanks for joining us,<br>
    The {{ config('app.name') }} Team
</x-mail::message>
