<?php
namespace Socieboy\Forum\Controllers;

use Illuminate\Routing\Controller;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Socieboy\Forum\Entities\Replies\ReplyRepo;
use Socieboy\Forum\Jobs\CheckCorrectAnswer;
use Socieboy\Forum\Jobs\Replies\PostReply;
use Socieboy\Forum\Jobs\Replies\UpdateReply;
use Socieboy\Forum\Jobs\SetCorrectAnswerStatus;
use Socieboy\Forum\Requests\CreateReplyRequest;
use Socieboy\Forum\Requests\DeleteReplyRequest;
use Socieboy\Forum\Requests\CorrectAnswerRequest;
use Socieboy\Forum\Requests\UpdateReplyRequest;
use Socieboy\Forum\Jobs\Replies\SubscribeUserToThread;

class RepliesController extends Controller
{

    use DispatchesJobs;

    /**
     * Implements the reply
     *
     * @param ReplyRepo $replyRepo
     */
    function __construct(ReplyRepo $replyRepo)
    {
        $this->middleware('auth');
        $this->replyRepo = $replyRepo;
    }

    /**
     * Store a new conversation.
     *
     * @param CreateReplyRequest $request
     * @param string             $slug
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(CreateReplyRequest $request, $slug)
    {
        $this->dispatch(new PostReply($request));

        return redirect()->route('forum.conversation.show', $slug);
    }

    /**
     * Display the reply edit form.
     *
     * @param string $slug
     * @param int    $reply_id
     *
     * @return \Illuminate\View\View
     */
    public function edit($slug, $reply_id)
    {
        $reply        = $this->replyRepo->find($reply_id);
        $conversation = $reply->conversation;

        return view('Forum::Replies.edit')->with(compact('reply', 'conversation'));
    }

    /**
     * Update an existing reply.
     *
     * @param UpdateReplyRequest $request
     * @param string             $slug
     * @param int                $reply_id
     *
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(UpdateReplyRequest $request, $slug, $reply_id)
    {
        $this->dispatch(new UpdateReply($request));
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
        $this->dispatch(new CheckCorrectAnswer($request));

        return redirect()->back();
    }


    public function destroy(DeleteReplyRequest $request, $slug, $reply_id)
    {
        $reply = $this->replyRepo->findOrFail($reply_id);
        $reply->delete();
        return redirect()->route('forum.conversation.show', $slug);
    }

}
