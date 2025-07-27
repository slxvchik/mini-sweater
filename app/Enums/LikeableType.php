<?

namespace App\Enums;

enum LikeableType: string {
    case TWEET = 'tweet';
    case COMMENT = 'comment';
    public static function values(): array {
        return array_column(self::cases(), 'value');
    }
}