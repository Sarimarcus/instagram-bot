<?php

namespace App\Classes;

use Illuminate\Support\Collection;
use App\Models\Bot;

/**
* Tools for playing with Instagram API
*/
class InstagramBot
{
    /*
     * Get and update information about a bot
     */
    public static function updateBotInformation(Bot $bot)
    {
        // Setting OAuth parameters
        self::setOAuth($bot);

        if ($user = self::runRequest($bot, 'getUsers', ['screen_name' => $bot->screen_name])) {
            $bot = Bot::find($user['id']);
            foreach ($bot->getFillable() as $p) {
                $bot->$p = $user[$p];
            }

            $bot->save();

            // Also fill the stats
            $stat = new Stat;
            $stat->bot_id = $user['id'];
            $stat->date = date('Y-m-d');
            foreach ($stat->getFillable() as $p) {
                $stat->$p = $user[$p];
            }

            \Log::info('[' . $bot->screen_name . '] Getting daily stats');
            $stat->save();
        }
    }

    /*
     * Run a task for every online bot
     */
    public static function runTask($task)
    {
        $bots = Bot::online()->orderBy('created_at')->get();
        foreach ($bots as $bot) {
            $bot::setConfiguration($bot);
            self::$task($bot);
        }
    }

    /*
     * Run a Instagram request
     */
    private static function runRequest(Bot $bot, $method, $params)
    {
        $defaultParams = [
            'format' => 'array'
        ];

        $params = array_merge($params, $defaultParams);

        // Let's try some random
        if (\App::environment('live')) {
            sleep(rand(0, 40));
        }

        try {
            if ($return = \Twitter::$method($params)) {
                Bot::isFine($bot);
                return $return;
            }
        } catch (\Exception $e) {
            \Log::error('[' . $bot->screen_name . '] Method ' . $method . ' : ' . $e->getMessage());
            Bot::addError($bot);
            return false;
        }
    }

    /*
     * Set OAuth parameters for the bot
     */
    private static function setOAuth(Bot $bot)
    {
        $instagram = new \Andreyco\Instagram\Client(array(
          'apiKey'      => $bot->client_id,
          'apiSecret'   => $bot->client_secret,
          'apiCallback' => 'http://instagram-bot.app'
        ));

       // Set user access token
        $instagram->setAccessToken($bot->access_token);

        // Get user info
        $user = $instagram->getUser();

        echo '<xmp>'; print_r($user); echo '</xmp>'; die();

        try {
            if (\Twitter::reconfig($botConfig)) {
                Bot::isFine($bot);
                return true;
            };
        } catch (\Exception $e) {
            \Log::error('[' . $bot->screen_name . '] Can\'t authentificate : ' . $e->getMessage());
            Bot::addError($bot);
        }
    }
}
