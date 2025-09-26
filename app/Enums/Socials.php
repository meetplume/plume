<?php

namespace App\Enums;

use BackedEnum;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;
use Illuminate\Contracts\Support\Htmlable;

enum Socials: string implements HasLabel, HasIcon
{
    case Bluesky = "bluesky";
    case Discord = "discord";
    case Facebook = "facebook";
    case Github = "github";
    case Instagram = "instagram";
    case Kick = "kick";
    case Linkedin = "linkedin";
    case Mastodon = "mastodon";
    case Pinterest = "pinterest";
    case Reddit = "reddit";
    case Snapchat = "snapchat";
    case Telegram = "telegram";
    case Tiktok = "tiktok";
    case Twitch = "twitch";
    case Whatsapp = "whatsapp";
    case X = "x";
    case Youtube = "youtube";

    public function getLabel(): string|Htmlable|null
    {
        return match ($this) {
            self::Bluesky => 'Bluesky',
            self::Discord => 'Discord',
            self::Facebook => 'Facebook',
            self::Github => 'Github',
            self::Instagram => 'Instagram',
            self::Kick => 'Kick',
            self::Linkedin => 'Linkedin',
            self::Mastodon => 'Mastodon',
            self::Pinterest => 'Pinterest',
            self::Reddit => 'Reddit',
            self::Snapchat => 'Snapchat',
            self::Telegram => 'Telegram',
            self::Tiktok => 'Tiktok',
            self::Twitch => 'Twitch',
            self::Whatsapp => 'Whatsapp',
            self::X => 'X',
            self::Youtube => 'Youtube',
        };
    }

    public function getIcon(): string|BackedEnum|null
    {
        return match ($this) {
            self::Bluesky =>'tabler-brand-bluesky',
            self::Discord => 'tabler-brand-discord',
            self::Facebook => 'tabler-brand-facebook',
            self::Github => 'tabler-brand-github',
            self::Instagram => 'tabler-brand-instagram',
            self::Kick => 'tabler-brand-kick',
            self::Linkedin => 'tabler-brand-linkedin',
            self::Mastodon => 'tabler-brand-mastodon',
            self::Pinterest => 'tabler-brand-pinterest',
            self::Reddit => 'tabler-brand-reddit',
            self::Snapchat => 'tabler-brand-snapchat',
            self::Telegram => 'tabler-brand-telegram',
            self::Tiktok => 'tabler-brand-tiktok',
            self::Twitch => 'tabler-brand-twitch',
            self::Whatsapp => 'tabler-brand-whatsapp',
            self::X => 'tabler-brand-x',
            self::Youtube => 'tabler-brand-youtube',
        };
    }

    public function fill(): string
    {
        return match ($this) {
            self::Bluesky =>'tabler-brand-bluesky',
            self::Discord => 'tabler-brand-discord-f',
            self::Facebook => 'tabler-brand-facebook-f',
            self::Github => 'tabler-brand-github-f',
            self::Instagram => 'tabler-brand-instagram-f',
            self::Kick => 'tabler-brand-kick-f',
            self::Linkedin => 'tabler-brand-linkedin-f',
            self::Mastodon => 'tabler-brand-mastodon',
            self::Pinterest => 'tabler-brand-pinterest-f',
            self::Reddit => 'tabler-brand-reddit',
            self::Snapchat => 'tabler-brand-snapchat-f',
            self::Telegram => 'tabler-brand-telegram',
            self::Tiktok => 'tabler-brand-tiktok-f',
            self::Twitch => 'tabler-brand-twitch',
            self::Whatsapp => 'tabler-brand-whatsapp-f',
            self::X => 'tabler-brand-x-f',
            self::Youtube => 'tabler-brand-youtube-f',
        };
    }
}
