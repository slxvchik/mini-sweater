<?

namespace App\Enums;

enum LikeableType: string {
    case TWEET = 'Tweet';
    case COMMENT = 'Comment';
    public static function values(): array {
        return array_column(self::cases(), 'value');
    }
}