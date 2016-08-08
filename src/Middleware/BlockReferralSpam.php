<?php namespace Arcanedev\SpamBlocker\Middleware;

use Arcanedev\SpamBlocker\Contracts\SpamBlocker;
use Closure;
use Illuminate\Http\Request;

/**
 * Class     BlockReferralSpam
 *
 * @package  Arcanedev\LaravelReferralSpamBlocker\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BlockReferralSpam
{
    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    /** @var  \Arcanedev\SpamBlocker\Contracts\SpamBlocker */
    private $blocker;

    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * BlockReferralSpam constructor.
     *
     * @param  \Arcanedev\SpamBlocker\Contracts\SpamBlocker  $blocker
     */
    public function __construct(SpamBlocker $blocker)
    {
        $this->blocker = $blocker;
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure                  $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $referer = $request->headers->get('referer');

        if ($this->blocker->isBlocked($referer)) {
            return response('Unauthorized.', 401);
        }

        return $next($request);
    }
}
