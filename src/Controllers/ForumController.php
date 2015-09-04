<?php

namespace Socieboy\Forum\Controllers;

use \App\Http\Controllers\Controller;
use Socieboy\Forum\Entities\Conversations\ConversationRepo;

class ForumController extends Controller
{

    /**
     * @var ConversationRepo
     */
    protected $conversationRepo;

    /**
     * @param ConversationRepo $conversationRepo
     */
    function __construct(ConversationRepo $conversationRepo)
    {
        $this->conversationRepo = $conversationRepo;
    }

    /**
     * Display the main page of the forum.
     * All conversations are listed.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $conversations = $this->conversationRepo->latest()->paginate(5);
        return view('Forum::index', compact('conversations'));
    }



} 