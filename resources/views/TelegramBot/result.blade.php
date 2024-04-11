У лучших товаров, похожих на ID: {{$productIdFromString ?? "XXX"}}

oopen_to_cart_percent - {{$result['data']['oopen_to_cart_percent'] ?? "XXX"}}
open_card_to_order_percent - {{$result['data']['open_card_to_order_percent'] ?? "XXX"}}
cart_to_order_percent - {{$result['data']['cart_to_order_percent'] ?? "XXX"}}

<code>resources/views/TelegramBot/result.blade.php</code>
@if(isset($user_not_member))
Подпишитесь на канал, чтобы сделать запрос
@endif