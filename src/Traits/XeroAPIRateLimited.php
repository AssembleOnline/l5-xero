<?php
namespace Assemble\l5xero\Traits;
use Cache;
use Carbon\Carbon;
use Log;

trait XeroAPIRateLimited {

    /**
     * Return success or release the job back to the queue with a delay
     * 
     * @return boolean|void
     */
    private function rateLimit_canRun() {
        $_rl_rate_limits = config('xero.rate_limits');
        $_rl_cache_key = config('xero.rate_limit_cache_key');

        $history = Cache::rememberForever($_rl_cache_key, function() {
            return [];
        });

        //ensure history sorted desc
        rsort($history);
        // current time running
        

        // add to history ( track inclusive of current job)
        foreach($_rl_rate_limits as $rateLimit) {

            $limited = $this->rateLimit_testLimit($rateLimit, $history);
            if($limited) {
                // weve hit a limit, log and kill
                Log::error("Xero Rate Limit hit: ".$rateLimit[0]."/".$rateLimit[1]." ... Delaying Job");

                // Update the cached history
                array_unshift($history, Carbon::now()->timestamp);
                Cache::forever($_rl_cache_key, $history);

                // requeue job with +1sec grace
                $this->release($rateLimit[1]+1);
                exit;
            }
        }
        // Update the cached history
        array_unshift($history, Carbon::now()->timestamp);
        Cache::forever($_rl_cache_key, $history);
        return true;
    }

    /**
     * Return success or release the job back to the queue with a delay
     * 
     * @param Array $limit
     * @param Carbon\Carbon $moment
     * @param Array $history
     * 
     * @return boolean
     */
    private function rateLimit_testLimit($limit, $history) {
        // get lowest allowed timestamp(s) (ie: 60s ago if we are limiting to 60s)
        $since = Carbon::now()->subSeconds($limit[1])->timestamp;

        $jobs_ran = array_filter($history, function($x) use ($since) {
            return $x >= $since;
        });

        // Exceeeded limit or not
        return count($jobs_ran) > $limit[0];
    }



    /**
     * Handle a job failure.
     *
     * @return void
     */
    // public function failed($err)
    // {
    //     // test for rate limited
    //     Log::error($err);
    // }
}