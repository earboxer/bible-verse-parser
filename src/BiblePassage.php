<?php

declare(strict_types=1);

namespace TechWilk\BibleVerseParser;

class BiblePassage
{
    protected $from;
    protected $to;

    public function __construct(BibleReference $from, BibleReference $to)
    {
        $this->from = $from;
        $this->to = $to;
    }

    public function from(): BibleReference
    {
        return $this->from;
    }

    public function to(): BibleReference
    {
        return $this->to;
    }

    /**
     * string formats.
     *
     * John
     * John 3
     * John 3:16
     * John 3:16-17
     * John 3:16-4:1
     * John 3:16 - Acts 1:1 // always has a verse
     */
    public function __toString(): string
    {
        $string = $this->from->book()->name();

        // Format "John"
        if (
            $this->to->book()->name() === $this->from->book()->name()
            && 1 === $this->from->chapter()
            && 1 === $this->from->verse()
            && $this->to->book()->chaptersInBook() === $this->to->chapter()
            && $this->to->book()->versesInChapter($this->to->chapter()) === $this->to->verse()
        ) {
            return $string;
        }

        $string .= ' '.$this->from->chapter();

        // Format "John 3"
        if (
            $this->to->book()->name() === $this->from->book()->name()
            && $this->to->chapter() === $this->from->chapter()
            && (
                0 === $this->from->verse()
                || (
                    1 === $this->from->verse()
                    && $this->to->book()->versesInChapter($this->to->chapter()) === $this->to->verse()
                )
            )
        ) {
            return $string;
        }

        $string .= ':'.$this->from->verse();

        // Format "John 3:16"
        if (
            $this->to->book()->name() === $this->from->book()->name()
            && $this->to->chapter() === $this->from->chapter()
            && $this->to->verse() === $this->from->verse()
        ) {
            return $string;
        }

        // Format "John 3:16-17"
        if ($this->from->chapter() === $this->to->chapter()) {
            return $string.'-'.$this->to->verse();
        }

        $toString = $this->to->chapter().':'.$this->to->verse();

        // Format "John 3:16 - Acts 1:1"
        if ($this->from->book()->name() !== $this->to->book()->name()) {
            return $string.' - '.$this->to->book()->name().' '.$toString;
        }

        return $string.'-'.$toString;
    }
}
