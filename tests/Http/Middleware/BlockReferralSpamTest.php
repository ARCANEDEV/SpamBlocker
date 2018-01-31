<?php namespace Arcanedev\SpamBlocker\Tests\Http\Middleware;

use Arcanedev\SpamBlocker\Tests\TestCase;

/**
 * Class     BlockReferralSpamTest
 *
 * @package  Arcanedev\SpamBlocker\Tests\Http\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BlockReferralSpamTest extends TestCase
{
    /* -----------------------------------------------------------------
     |  Tests
     | -----------------------------------------------------------------
     */

    /** @test */
    public function it_can_allow_request()
    {
        $response = $this->get('/', [
            'HTTP_REFERER' => 'http://www.google.com',
        ]);

        $response->assertSuccessful();
    }

    /** @test */
    public function it_must_block_spammer_request()
    {
        $response = $this->get('/', [
            'HTTP_REFERER' => 'http://www.0n-line.tv',
        ]);

        $response->assertStatus(401);
    }

    /** @test */
    public function it_must_block_spammer_request_with_subdomain()
    {
        $response = $this->get('/', [
            'HTTP_REFERER' => 'http://cubook.supernew.org',
        ]);

        $response->assertStatus(401);
    }
}
