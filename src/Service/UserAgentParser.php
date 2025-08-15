<?php
declare(strict_types=1);

namespace Djdmg\TransparentPixelBundle\Service;

use UAParser\Parser;

final class UserAgentParser
{
    private Parser $parser;
    public function __construct() { $this->parser = Parser::create(); }

    /** @return array{os:?string,browser:?string,device:?string,isMobile:bool,isBot:bool} */
    public function parse(?string $ua): array
    {
        if (!$ua) return ['os'=>null,'browser'=>null,'device'=>null,'isMobile'=>false,'isBot'=>false];

        $r = $this->parser->parse($ua);
        $os = trim(($r->os->family ?? '').' '.($r->os->toVersion() ?? ''));
        $browser = trim(($r->ua->family ?? '').' '.($r->ua->toVersion() ?? ''));
        $device = $r->device->family ?? null;

        $isMobile = stripos($ua, 'Mobile') !== false || stripos($ua, 'Android') !== false || stripos($ua, 'iPhone') !== false;
        $isBot = (bool) preg_match('~bot|crawler|spider|slurp|bingpreview|facebookexternalhit|linkedinbot|embedly|quora link preview|whatsapp~i', $ua);

        return ['os'=>$os ?: null, 'browser'=>$browser ?: null, 'device'=>$device, 'isMobile'=>$isMobile, 'isBot'=>$isBot];
    }
}
