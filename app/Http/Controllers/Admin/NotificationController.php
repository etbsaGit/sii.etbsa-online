<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends AdminController
{

    public function index()
    {
        $unReadNotifications = auth()->user()->unReadNotifications;
        $readNotifications = auth()->user()->readNotifications;

        return $this->sendResponseOk(compact('unReadNotifications', 'readNotifications'), "notification user.");
    }

    public function destroy(DatabaseNotification $notification)
    {
        $notification->markAsRead();

        return $this->sendResponseOk([], "Notificacion Leida.");
    }
}
