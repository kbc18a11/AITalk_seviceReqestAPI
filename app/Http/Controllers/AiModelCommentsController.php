<?php

namespace App\Http\Controllers;

use App\AiModel;
use App\AiModelComments;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AiModelCommentsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     * @param \Illuminate\Http\Request $request
     * @param int $aiModel_id コメントをするAiモデルのid
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request, int $ai_model_id)
    {
        //
        //バリデーションの検証
        $validationResult = AiModelComments::createValidator([
            'ai_model_id' => $ai_model_id,
            'comment' => $request->comment
        ]);
        //バリデーションの結果が駄目か？
        if ($validationResult->fails()) {
            # code...
            return response()->json([
                'createResult' => false,
                'error' => $validationResult->messages()
            ], 422);
        }
        //コメントを保存
        $createParam = [
            'ai_model_id' => $ai_model_id,
            'user_id' => Auth::id(),
            'comment' => $request->comment
        ];
        $createdComment = AiModelComments::create($createParam);
        //新しく作成したコメントのデータも返す
        return response()->json(
            ['createResult' => true,
                'createdComment' => $createdComment]);
    }

    /**
     * Aiモデルのid(ai_model_id)ごとにコメントを取得
     *
     * @param int $ai_model_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(int $ai_model_id)
    {
        //指定されたAIモデルのidからインスタンス化
        $aiModel = AiModel::find($ai_model_id);

        //指定したAIモデルは存在していないか？
        if (!$ai_model_id) {
            return response()->json([
                'error' => ['id' => '存在しないAIモデルです']
            ], 422);
        }

        return response()->json($aiModel->getComments());
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, int $id)
    {
        $aiModelComments = AiModelComments::find($id);
        //指定したidのコメントは存在していなか？
        // &&　指定したコメントのuserとリクエストしたユーザーとのidは違うか？
        if (!$aiModelComments
            || $aiModelComments->user_id !== Auth::id()) {
            return response()->json([
                'updateResult' => false,
                'error' => ['id' => '更新できないコメントです']
            ], 422);
        }

        //バリデーションの検証
        $validationResult =
            AiModelComments::updateValidator($request->all());
        //バリデーションの結果が駄目か？
        if ($validationResult->fails()) {
            # code...
            return response()->json([
                'updateResult' => false,
                'error' => $validationResult->messages()
            ], 422);
        }

        //コメントの更新を実行
        $aiModelComments->update($request->all());
        return response()->json(['updateResult' => true]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Request $request, $id)
    {
        //
        $aiModelComments = AiModelComments::find($id);
        //指定したidのコメントは存在していなか？
        // &&　指定したコメントのuserとリクエストしたユーザーとのidは違うか？
        if (!$aiModelComments
            || $aiModelComments->user_id !== Auth::id()) {
            return response()->json([
                'deleteResult' => false,
                'error' => ['id' => '削除できないコメントです']
            ], 422);
        }

        //コメントの削除を実行
        $aiModelComments->delete();
        return response()->json(['deleteResult' => true]);
    }
}
