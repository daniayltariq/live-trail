<?php

use Illuminate\Support\Facades\Broadcast;

Broadcast::channel('owner', function ($user) {
    // return (int) $user->owner === 1;
    return true;
});
