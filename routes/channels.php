<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('kitchen-orders', function ($user) {
    return true;
});
