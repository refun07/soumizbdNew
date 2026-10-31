<?php

namespace Tests\Unit;

use App\Services\StorefrontTestimonials;
use PHPUnit\Framework\TestCase;

class StorefrontTestimonialsTest extends TestCase
{
    public function test_text_reviews_do_not_require_photos_or_invent_ratings(): void
    {
        $rows=StorefrontTestimonials::rows([],['Customer'],[],['Original customer wording.'],[],fn()=> $this->fail('An absent avatar must not be resolved'));
        $this->assertCount(1,$rows);
        $this->assertSame('Original customer wording.',$rows[0]['text']);
        $this->assertSame('',$rows[0]['avatar']);
        $this->assertSame('',$rows[0]['rating']);
    }

    public function test_blank_rows_are_excluded_and_invalid_ratings_are_not_shown(): void
    {
        $rows=StorefrontTestimonials::rows(['12','',''],['First','','Third'],['Student','',''],['Review one','','Review three'],['5','','9'],fn($id)=>'/portrait/'.$id);
        $this->assertCount(2,$rows);
        $this->assertSame('/portrait/12',$rows[0]['avatar']);
        $this->assertSame('5',$rows[0]['rating']);
        $this->assertSame('',$rows[1]['rating']);
        $this->assertSame('Third',$rows[1]['name']);
    }
}
