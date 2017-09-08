<?php namespace Arcanedev\SpamBlocker\Http\Middleware;

use Arcanedev\SpamBlocker\Contracts\SpamBlocker;
use Closure;
use Illuminate\Http\Request;

/**
 * Class     BlockReferralSpam
 *
 * @package  Arcanedev\SpamBlocker\Http\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BlockReferralSpam
{
    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    /** @var  \Arcanedev\SpamBlocker\Contracts\SpamBlocker */
    protected $blocker;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
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

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
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

        return $this->blocker->isBlocked($referer)
            ? $this->getBlockedResponse()
            : $next($request);
    }

    /* -----------------------------------------------------------------
     |  Other Methods
     | -----------------------------------------------------------------
     */

    /**
     * Get the blocked referer's response.
     *
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     */
    protected function getBlockedResponse()
    {
        return response('Unauthorized.', 401);
    }
}
