<?php

namespace App\Http\Controllers\AI;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use OpenAI\Laravel\Facades\OpenAI;

class OpenAIController extends Controller
{
    # generate ai contents
    public function generateOpenAIContent(Request $request)
    {
        $result = OpenAI::chat()->create([
            'model' => 'gpt-3.5-turbo',
            'messages' => [
                ['role' => 'user', 'content' => $request->prompt],
            ],
        ]);

        return $result->choices[0]->message->content;
    }
}
