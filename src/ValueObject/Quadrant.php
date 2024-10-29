<?php

declare(strict_types=1);

namespace App\ValueObject;

/**
 * Object representing a quadrant of a Cistercian number.
 * A quadrant is a corner of a cistercian number, which is divided into four quadrants.
 *
 * The top right quadrant is for the unit digit, the top left quadrant is for the tenth digit,
 * The bottom right quadrant is for the hundredth digit and the bottom left quadrant is for the thousandth digit.
 *
 * For more info @see https://en.wikipedia.org/wiki/Cistercian_numerals
 */
final readonly class Quadrant
{
    private function __construct(
        public int $x1,
        public int $y1,
        public int $x2,
        public int $y2,
        public int $xOffset,
        public int $yOffset,
    ) {
    }

    public static function createForTopRight(int $segmentLength, int $offset): self
    {
        return new self(
            x1: $segmentLength,
            y1: 0,
            x2: $segmentLength * 2,
            y2: $segmentLength,
            xOffset: $offset,
            yOffset: $offset,
        );
    }

    public static function createForTopLeft(int $segmentLength, int $offset): self
    {
        return new self(
            x1: $segmentLength,
            y1: 0,
            x2: 0,
            y2: $segmentLength,
            xOffset: -$offset,
            yOffset: $offset,
        );
    }

    public static function createForBottomLeft(int $segmentLength, int $offset): self
    {
        return new self(
            x1: $segmentLength,
            y1: $segmentLength * 4,
            x2: 0,
            y2: $segmentLength * 3,
            xOffset: -$offset,
            yOffset: -$offset,
        );
    }

    public static function createForBottomRight(int $segmentLength, int $offset): self
    {
        return new self(
            x1: $segmentLength,
            y1: $segmentLength * 4,
            x2: $segmentLength * 2,
            y2: $segmentLength * 3,
            xOffset: $offset,
            yOffset: -$offset,
        );
    }
}
