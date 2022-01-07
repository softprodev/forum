<?php
namespace Socieboy\Forum\Jobs\Conversations;

use App\Jobs\Job;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Socieboy\Forum\Events\NewConversation;
use Illuminate\Contracts\Queue\ShouldQueue;
use EasySlug\EasySlug\EasySlugFacade as Slug;
use Socieboy\Forum\Entities\Conversations\ConversationRepo;

class StartConversation extends Job implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * @var string
     */
    protected $topic_id;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $message;

    /**
     * Create a new job instance.
     *
     * @param Request $request
     */
    function __construct($request)
    {
        $this->topic_id  = $request->topic_id;
        $this->title     = strip_tags($request->title);
        $this->message   = strip_tags($request->message);
    }

    /**
     * Execute the job.
     *
     * @param ConversationRepo $conversationRepo
     *
     * @return void
     */
    public function handle(ConversationRepo $conversationRepo)
    {
        $conversation = $conversationRepo->model();
        $conversation->fill($this->prepareDate());
        $conversation->save();

        if (config('forum.events.fire')) {
            event(new NewConversation($conversation));
        }
    }

    /**
     * Prepare array to fill the conversation model.
     *
     * @return array
     */
    public function prepareDate()
    {
        $databasePrefix = (config('forum.database.prefix') ? config('forum.database.prefix') . '_' : '');

        return [
            'user_id'  => auth()->user()->id,
            'title'    => $this->title,
            'topic_id' => $this->topic_id,
            'message'  => $this->message,
            'slug'     => Slug::generateUniqueSlug($this->title, $databasePrefix . 'conversations'),
        ];
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
