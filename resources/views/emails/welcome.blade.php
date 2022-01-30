@component('mail::message')
# Подтверждение регистрации

Здравствуйте,{{ $name }}!

Добро пожаловать в наш Магазин, требуется актировать вашу почту для дальнейшей работы в нашей системе.

@component('mail::button', ['url' => "http://localhost:8080/mail/activate/$userId-$token"])
Подтвердить почту
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
