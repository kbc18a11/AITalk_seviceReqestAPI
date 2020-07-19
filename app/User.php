<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Contracts\JWTSubject;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'icon'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * 登録のバリデーションの条件
     * @var array
     */
    private static $createRules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255','unique:users'],
        'password' => ['required', 'string', 'min:8', 'confirmed'],
    ];

    /**
     * ユーザ情報の更新用
     * @var array
     */
    private static $updateRules = [
        'name' => ['required', 'string', 'max:255'],
        'email' => ['required', 'string', 'email', 'max:255'],
        'icon' => ['image']
    ];

    /**
     * バリデーションルールごとののエラーメッセージ
     * @var array
     */
    private static $ErrorMessages = [
        'required' => '必須項目です。',
        'max' => '255文字以下入力してください',
        'min' => '8文字以上入力してください',
        'unique' => '既にほかのユーザーが利用しています',
        'email' => 'メールアドレスを入力してください',
        'confirmed' => 'パスワードの確認入力が一致しません',
        'image' => '画像を指定してください'
    ];

    /**
     * ユーザー登録のパラメータのバリデーションの検証
     * @param array
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function createValidator(array $array)
    {
        # code...
        return Validator::make($array, self::$createRules, self::$ErrorMessages);
    }

    /**
     * ユーザー情報更新のパラメータのバリデーションの検証
     * @param array $array
     * @return \Illuminate\Contracts\Validation\Validator
     */
    public static function updateValidator(array $array)
    {
        # code...
        return Validator::make($array, self::$updateRules, self::$ErrorMessages);
    }

    /***
     * 引数のメールアドレスが既に自分以外の誰かに使用されているかの検証
     * @param string $email 検証対象のメールアドレス
     * @return bool メールアドレスの検証結果
     */
    public function otherPeopleUseEmail(string $email): bool
    {
        //指定されたemailを使用したカラムは存在するか？
        $user = self::where('email', $email)->first();
        //既に使用されたemailか？ && 使用されているemailのユーザーiｄは、更新対象のユーザーidと同じではないか？
        return $user && $user->id !== $this->id;
    }

    /**
     * JWT トークンに保存する ID を返す
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * JWT トークンに埋め込む追加の情報を返す
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
