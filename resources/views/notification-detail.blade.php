@if($notification->notifiable_type == 'ProductRequest')
    @include('partials.notification.product-request')
@endif

@if($notification->notifiable_type == 'PurchaseOrder')
    @include('partials.notification.purchase-order')
@endif