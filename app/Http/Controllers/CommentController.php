<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminRequest;
use App\Http\Requests\SendCommentRequest;
use App\Models\Comment;
use App\Models\Shop;
use App\Models\Showcase;
use App\Models\User;
use Illuminate\Http\Request;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

class CommentController extends Controller
{
    protected $telegram;

    public function __construct()
    {
        $this->telegram = new Api(config('telegram.bot_token'));
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return inertia('Dashboard/Comment/Index', [
            'comments' => Comment::with('user:id,name', 'showcase:id,name,shop_id', 'showcase.shop:id,name',)->latest('created_at')->paginate(),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Comment $comment)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {
        //
    }

    public function send(SendCommentRequest $sendCommentRequest)
    {
        $filePath = "";
        if ($sendCommentRequest->hasFile('doc')) {
            $filePath = $sendCommentRequest->file('doc')->store('documents');
            $filePath = "/storage/$filePath";
        }
        $comment = Comment::create($sendCommentRequest->except('doc') + [
            'user_id' => auth()->user()->id,
            'document' => $filePath,
        ]);

        $showcase = Showcase::find($sendCommentRequest->showcase_id);
        $shop = Shop::find($showcase->shop_id);
        $admins = User::select('telegram_chat_id', 'role')->whereRole('admin')->where('telegram_chat_id', '<>', NULL)->get();
        foreach ($admins as $admin) {
            try {
                if (empty($filePath)) {
                    $this->telegram->sendMessage([
                        'chat_id' => $admin->telegram_chat_id,
                        'text' => "Комментария: " . $comment->comment . "\nОт: " . auth()->user()->name . "\nТорговая Точка: " . $shop->name . "\nВитрина: " . $showcase->name,
                    ]);
                } else {
                    $this->telegram->sendDocument([
                        'chat_id' => $admin->telegram_chat_id,
                        'document' => new InputFile(public_path($filePath)),
                        'caption' => "Комментария: " . $comment->comment . "\nОт: " . auth()->user()->name . "\nТорговая Точка: " . $shop->name . "\nВитрина: " . $showcase->name,
                    ]);
                }
            } catch (\Throwable $th) {
                dd($th->getMessage());
            }
        }

        return response()->json([
            'message' => 'Ваш комментария успешно отправлен'
        ]);
    }
}
