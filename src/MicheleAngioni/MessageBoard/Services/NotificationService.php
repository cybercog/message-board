<?php namespace MicheleAngioni\MessageBoard\Services;

use Illuminate\Support\Collection;
use MicheleAngioni\MessageBoard\Contracts\NotificationRepositoryInterface as NotificationRepo;
use MicheleAngioni\MessageBoard\Models\Notification;
use RuntimeException;

class NotificationService
{
    protected $notificationRepo;

    function __construct(NotificationRepo $notificationRepo)
    {
        $this->notificationRepo = $notificationRepo;
    }

    /**
     * Return a collection of all available Notifications.
     *
     * @return Collection
     */
    public function getNotifications()
    {
        return $this->notificationRepo->all();
    }

    /**
     * Return input Notification.
     *
     * @param  int  $idNotification
     * @return Notification
     */
    public function getNotification($idNotification)
    {
        return $this->notificationRepo->findOrFail($idNotification);
    }

    /**
     * Retrieve and return all notifications of input User.
     *
     * @param  int  $toId
     * @return Collection
     */
    public function getUserNotifications($toId)
    {
        return $this->notificationRepo->getBy(['to_id' => $toId]);
    }

    /**
     * Create a new Notification.
     *
     * @param  int  $receiverId
     * @param  string|null  $fromType
     * @param  int|null  $senderId
     * @param  string|null  $type
     * @param  string  $text
     * @param  string  $picUrl
     * @param  string  $url
     * @param  array  $extra
     * @throws RuntimeException
     *
     * @return Notification
     */
    public function sendNotification($receiverId, $fromType = null, $senderId = null, $type = null, $text, $picUrl = null, $url = null, array $extra = [])
    {
        try {
            $notification = $this->notificationRepo->create([
                'from_id' => $senderId,
                'from_type' => $fromType,
                'to_id' => $receiverId,
                'type' => $type,
                'text' => $text,
                'pic_url' => $picUrl,
                'url' => $url,
                'extra' => json_encode($extra)
            ]);
        } catch (\Exception $e) {
            throw new \RuntimeException("Caught RuntimeException in ".__METHOD__.' at line '.__LINE__.': ' .$e->getMessage());
        }

        return $notification;
    }

    /**
     * Read input notification.
     * Return true on success.
     *
     * @param  int  $idNotification
     * @return bool
     */
    public function readNotification($idNotification)
    {
        $notification = $this->notificationRepo->findOrFail($idNotification);
        $notification->setAsRead();

        return true;
    }

    /**
     * Read all notifications of input user.
     *
     * @param  int  $toId
     * @return int
     */
    public function readAllNotifications($toId)
    {
        return $this->notificationRepo->updateBy([
            'to_id'=> $toId,
            'read' => false
        ], [
            'read' => true
        ]);
    }

    /**
     * Delete all old notification older than input datetime 'Y-m-d H:i:s' .
     *
     * @param  int  $datetime
     * @return mixed
     */
    public function deleteOldNotifications($datetime)
    {
        return $this->notificationRepo->deleteOldNotifications($datetime);
    }

}
