<?php
namespace Socieboy\Forum\Controllers;

use App\Http\Controllers\Controller;
use Socieboy\Forum\Jobs\Replies\SubscribeUserToThread;
use Socieboy\Forum\Jobs\SetCorrectAnswerStatus;
use Socieboy\Forum\Requests\CorrectAnswerRequest;
use Socieboy\Forum\Requests\CreateReplyRequest;

class RepliesController extends Controller
{
    /**
     * Implements the reply
     */
    function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Store a new conversation.
     *
     * @param CreateReplyRequest $request
     * @param string $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateReplyRequest $request, $slug)
    {
        $this->dispatchFrom('Socieboy\Forum\Jobs\Replies\PostReply', $request);

        return redirect()->route('forum.conversation.show', $slug);
    }

    /**
     * Set the correct answer
     *
     * @param CorrectAnswerRequest $request
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function correctAnswer(CorrectAnswerRequest $request)
    {
        $this->dispatchFrom('Socieboy\Forum\Jobs\CheckCorrectAnswer', $request);

        return redirect()->back();
    }
}
