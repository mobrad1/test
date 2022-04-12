<?php

namespace App\Listeners;

use App\Events\AchievementUnlocked;
use App\Models\Badge;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UnlockBadge
{
    /**
     * Handle the event.
     *
     * @param AchievementUnlocked $event
     * @return void
     */
    public function handle(AchievementUnlocked $event)
    {
        $badgeToUnlock = app('badges')->filter(function($badge) use ($event){
            //If user is qualified for the badge dispatch the event badge unlocked
            if($badge->qualifier($event->user)){
                \App\Events\BadgeUnlocked::dispatch($badge->title(),$event->user->fresh());
            }
            return $badge->qualifier($event->user);
        })->map(function ($badge){
            return $badge->modelKey();
        });;
        Badge::find($badgeToUnlock->first())->unlock($event->user);
    }
}
