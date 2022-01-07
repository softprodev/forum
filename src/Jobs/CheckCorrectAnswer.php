<?php
namespace Socieboy\Forum\Jobs;

use App\Jobs\Job;
use Socieboy\Forum\Events\BestAnswer;
use Illuminate\Queue\SerializesModels;
use Socieboy\Forum\Entities\Replies\ReplyRepo;

class CheckCorrectAnswer extends Job
{
    use SerializesModels;

    /**
     * @var int
     */
    protected $reply_id;

    /**
     * @param Request $request
     */
    function __construct($request)
    {
        $this->reply_id = $request->reply_id;
    }

    /**
     * @param ReplyRepo $replyRepo
     */
    public function handle(ReplyRepo $replyRepo)
    {
        $reply = $replyRepo->findOrFail($this->reply_id);
        $reply->correct_answer = !$reply->correct_answer;
        $reply->save();

        if (config('forum.events.fire')) {
            event(new BestAnswer($reply));
        }
    }

    /**
     * Return true if the auth user is the owner of the conversation where the reply was left
     *
     * @param $conversation
     *
     * @return bool
     */
    protected function authUserIsOwner($conversation)
    {
        return auth()->user()->id == $conversation->user_id;
    }
}
