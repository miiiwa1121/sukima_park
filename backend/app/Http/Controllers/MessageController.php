<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ============================================================
 * メッセージコントローラー (MessageController.php)
 * ============================================================
 * 
 * DM（ダイレクトメッセージ）機能を担当するコントローラー
 * 
 * 【対応画面】
 *   - message_list_screen.blade.php（メッセージ一覧画面）
 *   - message_detail_screen.blade.php（メッセージ詳細画面）
 * 
 * 【主な機能】
 *   - 会話一覧の表示
 *   - 会話詳細の表示
 *   - メッセージの送信
 * 
 * 【使用テーブル】
 *   - CHAT_TABLE（チャットテーブル）
 *   - MEMBER_TABLE（会員テーブル）
 * 
 * ============================================================
 */
class MessageController extends Controller
{
    /**
     * メッセージ一覧を表示
     * 
     * 【処理内容】
     * 1. ログインユーザーが関与する会話を取得
     * 2. 相手ユーザーごとにグループ化
     * 3. 各会話の最新メッセージと未読数を取得
     * 
     * @return \Illuminate\Contracts\View\View
     */
    public function index()
    {
        $user = Auth::user();
        
        if (!$user) {
            return redirect()->route('login');
        }
<?php

namespace App\Http\Controllers;

use App\Models\Chat;
use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * ============================================================
 * メッセージコントローラー (MessageController.php)
 * ============================================================
 */
class MessageController extends Controller
{
    /**
     * メッセージ一覧を表示
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userId = $user->USER_ID;
        $conversations = $this->conversationPartners($userId);

        $messages = $this->buildConversationSummaries($conversations, $userId)
            ->sortByDesc('last_message_date')
            ->values();

        return view('message_list_screen', compact('messages'));
    }

    /**
     * API: メッセージ一覧を取得
     */
    public function indexApi()
    {
        $userId = Auth::user()->USER_ID;
        $conversations = $this->conversationPartners($userId);

        $messages = $this->buildConversationSummaries($conversations, $userId)
            ->sortByDesc('last_message_date')
            ->values();

        return response()->json([
            'conversations' => $messages,
        ]);
    }

    /**
     * 新規メッセージ作成画面を表示
     */
    public function create()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        return view('message_create_screen');
    }

    /**
     * ユーザー検索API
     */
    public function search(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $query = $request->query('q', '');

        if (strlen($query) < 1) {
            return response()->json([]);
        }

        $users = Member::where('USER_ID', '!=', $user->USER_ID)
            ->where('ACCOUNT_STATUS', 0)
            ->where('USERNAME', 'LIKE', '%' . $query . '%')
            ->limit(10)
            ->get()
            ->map(function ($member) {
                return [
                    'id' => $member->USER_ID,
                    'name' => $member->USERNAME,
                    'email' => $member->EMAIL,
                    'initial' => mb_substr($member->USERNAME, 0, 1),
                ];
            });

        return response()->json($users);
    }

    /**
     * 会話詳細を表示
     */
    public function show($partnerId)
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login');
        }

        $userId = $user->USER_ID;

        $recipient = Member::find($partnerId);

        if (!$recipient) {
            abort(404, 'ユーザーが見つかりません');
        }

        $messages = $this->conversationMessages($userId, $partnerId)
            ->map(function ($chat) use ($userId) {
                return (object)[
                    'id' => $chat->CHAT_ID,
                    'content' => $chat->MESSAGE,
                    'created_at' => \Carbon\Carbon::parse($chat->DATE->format('Y-m-d') . ' ' . $chat->TIME),
                    'is_sent' => $chat->USER_ID_FROM == $userId,
                    'image' => $chat->IMAGE,
                ];
            });

        $recipient = (object)[
            'id' => $recipient->USER_ID,
            'name' => $recipient->USERNAME,
            'icon_image' => $recipient->ICON_IMAGE,
        ];

        return view('message_detail_screen', compact('messages', 'recipient'))->with('hideFooter', true);
    }

    /**
     * API: 会話詳細を取得
     */
    public function showApi($partnerId)
    {
        $userId = Auth::user()->USER_ID;

        $recipient = Member::find($partnerId);

        if (!$recipient) {
            return response()->json([
                'message' => 'ユーザーが見つかりません。',
            ], 404);
        }

        $messages = $this->conversationMessages($userId, $partnerId)
            ->map(function ($chat) use ($userId) {
                return [
                    'id' => $chat->CHAT_ID,
                    'content' => $chat->MESSAGE,
                    'created_at' => \Carbon\Carbon::parse($chat->DATE->format('Y-m-d') . ' ' . $chat->TIME)->format('Y-m-d H:i:s'),
                    'is_sent' => $chat->USER_ID_FROM == $userId,
                    'image' => $chat->IMAGE,
                ];
            });

        return response()->json([
            'recipient' => [
                'id' => $recipient->USER_ID,
                'name' => $recipient->USERNAME,
                'icon_image' => $recipient->ICON_IMAGE,
            ],
            'messages' => $messages,
        ]);
    }

    /**
     * メッセージを送信
     */
    public function store(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $validated = $request->validate([
            'recipient_id' => 'required|integer|exists:MEMBER_TABLE,USER_ID',
            'content' => 'required|string|max:512',
        ]);

        $now = now();

        Chat::create([
            'USER_ID_FROM' => $user->USER_ID,
            'USER_ID_TO' => $validated['recipient_id'],
            'MESSAGE' => $validated['content'],
            'IMAGE' => null,
            'YEAR' => $now->format('Y-m-d'),
            'DATE' => $now->format('Y-m-d'),
            'TIME' => $now->format('H:i:s'),
        ]);

        return response()->json(['success' => true]);
    }

    /**
     * API: メッセージ送信
     */
    public function storeApi(Request $request)
    {
        $validated = $request->validate([
            'recipient_id' => 'required|integer|exists:MEMBER_TABLE,USER_ID',
            'content' => 'required|string|max:512',
        ]);

        $now = now();

        $chat = Chat::create([
            'USER_ID_FROM' => Auth::user()->USER_ID,
            'USER_ID_TO' => $validated['recipient_id'],
            'MESSAGE' => $validated['content'],
            'IMAGE' => null,
            'YEAR' => $now->format('Y-m-d'),
            'DATE' => $now->format('Y-m-d'),
            'TIME' => $now->format('H:i:s'),
        ]);

        return response()->json([
            'message' => '送信しました。',
            'chat' => [
                'id' => $chat->CHAT_ID,
                'recipient_id' => $chat->USER_ID_TO,
                'content' => $chat->MESSAGE,
                'created_at' => $now->format('Y-m-d H:i:s'),
            ],
        ], 201);
    }

    /**
     * 新着メッセージを取得（ポーリング用API）
     */
    public function poll(Request $request, $partnerId)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $userId = $user->USER_ID;
        $lastId = $request->query('last_id', 0);

        $newMessages = $this->pollMessages($userId, $partnerId, $lastId)
            ->map(function ($chat) use ($userId) {
                return [
                    'id' => $chat->CHAT_ID,
                    'content' => $chat->MESSAGE,
                    'created_at' => \Carbon\Carbon::parse($chat->DATE->format('Y-m-d') . ' ' . $chat->TIME)->format('Y-m-d H:i:s'),
                    'time' => \Carbon\Carbon::parse($chat->TIME)->format('H:i'),
                    'is_sent' => $chat->USER_ID_FROM == $userId,
                ];
            });

        return response()->json([
            'messages' => $newMessages,
            'last_id' => $newMessages->isNotEmpty() ? $newMessages->last()['id'] : $lastId,
        ]);
    }

    /**
     * API: 新着メッセージを取得（ポーリング）
     */
    public function pollApi(Request $request, $partnerId)
    {
        $userId = Auth::user()->USER_ID;
        $lastId = $request->query('last_id', 0);

        $newMessages = $this->pollMessages($userId, $partnerId, $lastId)
            ->map(function ($chat) use ($userId) {
                return [
                    'id' => $chat->CHAT_ID,
                    'content' => $chat->MESSAGE,
                    'created_at' => \Carbon\Carbon::parse($chat->DATE->format('Y-m-d') . ' ' . $chat->TIME)->format('Y-m-d H:i:s'),
                    'is_sent' => $chat->USER_ID_FROM == $userId,
                ];
            })
            ->values();

        return response()->json([
            'messages' => $newMessages,
        ]);
    }

    private function conversationPartners(int $userId)
    {
        return DB::table('CHAT_TABLE')
            ->select(DB::raw('
                CASE 
                    WHEN USER_ID_FROM = ? THEN USER_ID_TO 
                    ELSE USER_ID_FROM 
                END as partner_id
            '))
            ->addBinding($userId, 'select')
            ->where('USER_ID_FROM', $userId)
            ->orWhere('USER_ID_TO', $userId)
            ->groupBy('partner_id')
            ->pluck('partner_id');
    }

    private function buildConversationSummaries($conversations, int $userId)
    {
        $messages = collect();

        foreach ($conversations as $partnerId) {
            $partner = Member::find($partnerId);
            if (!$partner) {
                continue;
            }

            $latestMessage = Chat::where(function ($query) use ($userId, $partnerId) {
                    $query->where('USER_ID_FROM', $userId)->where('USER_ID_TO', $partnerId);
                })
                ->orWhere(function ($query) use ($userId, $partnerId) {
                    $query->where('USER_ID_FROM', $partnerId)->where('USER_ID_TO', $userId);
                })
                ->orderByDesc('DATE')
                ->orderByDesc('TIME')
                ->first();

            if (!$latestMessage) {
                continue;
            }

            $messageDateTime = \Carbon\Carbon::parse($latestMessage->DATE->format('Y-m-d') . ' ' . $latestMessage->TIME);
            $timeAgo = $this->getTimeAgo($messageDateTime);

            $messages->push([
                'partner' => [
                    'id' => $partner->USER_ID,
                    'name' => $partner->USERNAME,
                    'icon_image' => $partner->ICON_IMAGE,
                ],
                'preview' => mb_strlen($latestMessage->MESSAGE) > 30
                    ? mb_substr($latestMessage->MESSAGE, 0, 30) . '...'
                    : $latestMessage->MESSAGE,
                'time_ago' => $timeAgo,
                'unread' => false,
                'unread_count' => 0,
                'last_message_date' => $messageDateTime,
            ]);
        }

        return $messages;
    }

    private function conversationMessages(int $userId, int $partnerId)
    {
        return Chat::where(function ($query) use ($userId, $partnerId) {
                $query->where('USER_ID_FROM', $userId)->where('USER_ID_TO', $partnerId);
            })
            ->orWhere(function ($query) use ($userId, $partnerId) {
                $query->where('USER_ID_FROM', $partnerId)->where('USER_ID_TO', $userId);
            })
            ->orderBy('DATE')
            ->orderBy('TIME')
            ->get();
    }

    private function pollMessages(int $userId, int $partnerId, int $lastId)
    {
        return Chat::where('CHAT_ID', '>', $lastId)
            ->where(function ($query) use ($userId, $partnerId) {
                $query->where(function ($q) use ($userId, $partnerId) {
                    $q->where('USER_ID_FROM', $userId)->where('USER_ID_TO', $partnerId);
                })->orWhere(function ($q) use ($userId, $partnerId) {
                    $q->where('USER_ID_FROM', $partnerId)->where('USER_ID_TO', $userId);
                });
            })
            ->orderBy('DATE')
            ->orderBy('TIME')
            ->get();
    }

    /**
     * 相対時間を取得
     */
    private function getTimeAgo($dateTime)
    {
        $now = now();
        $diff = $now->diffInMinutes($dateTime);

        if ($diff < 1) {
            return 'たった今';
        } elseif ($diff < 60) {
            return $diff . '分前';
        } elseif ($diff < 1440) {
            return floor($diff / 60) . '時間前';
        } elseif ($diff < 2880) {
            return '昨日';
        }

        return floor($diff / 1440) . '日前';
    }
}
        return response()->json(['success' => true]);
