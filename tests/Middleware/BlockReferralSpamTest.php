<?php namespace Arcanedev\SpamBlocker\Tests\Middleware;

use Arcanedev\SpamBlocker\Tests\TestCase;

/**
 * Class     BlockReferralSpamTest
 *
 * @package  Arcanedev\SpamBlocker\Tests\Middleware
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class BlockReferralSpamTest extends TestCase
{
    /* ------------------------------------------------------------------------------------------------
     |  Test Functions
     | ------------------------------------------------------------------------------------------------
     */
    /** @test */
    public function it_can_allow_request()
    {
        $this->get('/', [
            'HTTP_REFERER' => 'http://www.google.com',
        ]);

        $this->assertResponseOk();
    }

    /** @test */
    public function it_must_block_spammer_request()
    {
        $this->get('/', [
            'HTTP_REFERER' => 'http://www.0n-line.tv',
        ]);

        $this->assertResponseStatus(401);
    }

    /** @test */
    public function it_must_block_spammer_request_with_subdomain()
    {
        $this->get('/', [
            'HTTP_REFERER' => 'http://cubook.supernew.org',
        ]);

        $this->assertResponseStatus(401);
    }

    /** @test */
    public function it_must_block_spammer_request_with_subdomain_and_not_utf8()
    {
        $this->get('/', [
            'HTTP_REFERER' => 'http://с.новым.годом.рф',
        ]);

        $this->assertResponseStatus(401);
    }
}
