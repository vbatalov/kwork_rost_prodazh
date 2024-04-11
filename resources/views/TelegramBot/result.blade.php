У лучших товаров, похожих на ID: {{$productIdFromString ?? "XXX"}}

oopen_to_cart_percent - {{$result['data']['oopen_to_cart_percent'] ?? "XXX"}}
open_card_to_order_percent - {{$result['data']['open_card_to_order_percent'] ?? "XXX"}}
cart_to_order_percent - {{$result['data']['cart_to_order_percent'] ?? "XXX"}}

@if(isset($user_not_member))
<b>Информация скрыта, Вам необходимо подписаться на канал, а затем нажать "Я подписался".</b>
@endif
<code>resources/views/TelegramBot/result.blade.php</code>