# 通知システムの設定手順

## 1. データベーステーブルの作成

まず、以下のSQLファイルを実行してください：
`sql/create_notifications_table.sql`

phpMyAdminまたはMySQLコンソールで実行してください。

## 2. 通知システムのテスト

以下のURLにアクセスして通知システムをテストしてください：
http://localhost/j2025c/main/test_notifications.php

## 3. 確認事項

### 必要なテーブル：
- `notifications` - 通知データ
- `group_chats` - グループチャット
- `group_chat_messages` - グループチャットメッセージ  
- `group_chat_members` - グループメンバー
- `class_chats` - クラスチャット
- `community_invite_codes` - コミュニティ招待コード

### 通知が送信されるタイミング：
1. **クラスチャット**: `class_chat.php`でメッセージ送信時
2. **グループチャット**: `chat.php`でメッセージ送信時
3. **コミュニティ参加**: `community_join.php`で参加時
4. **コミュニティ作成**: `community_create.php`で作成時
5. **FAQ回答**: `teacher_reply.php`で回答時
6. **FAQ質問**: `faq_create.php`で質問投稿時

## 4. トラブルシューティング

### 通知が来ない場合：
1. データベースに`notifications`テーブルが存在するか確認
2. ログインユーザーのuser_idが正しく取得できているか確認
3. PHPエラーログを確認（通知作成時のデバッグ情報が出力される）
4. テスト通知機能で基本的な通知作成が動作するか確認

### デバッグ方法：
- `test_notifications.php`でテスト通知を作成
- PHPエラーログを確認（詳細なデバッグ情報が出力される）
- 通知画面で通知が表示されるか確認

## 5. 実装ファイル

### 通知システム関連：
- `common/notification_helper.php` - 通知作成関数
- `notification.php` - 通知表示画面
- `css/notification.css` - 通知画面スタイル
- `test_notifications.php` - テスト用ページ

### 通知が送信される機能：
- `class_chat.php` - クラスチャット
- `chat.php` - グループチャット  
- `community_join.php` - コミュニティ参加
- `community_create.php` - コミュニティ作成
- `faq_create.php` - FAQ質問投稿
- `teacher_reply.php` - FAQ回答
