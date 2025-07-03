<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"
            integrity="sha384-geWF76RCwLtnZ8qwWowPQNguL3RmwHVBC9FhGdlKrxdiJJigb/j/68SIy3Te4Bkz"
            crossorigin="anonymous"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@100..900&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-9ndCyUaIbzAi2FUVXJi0CjmCapSmO7SnpJef0486qhLnuZ2cdeRhO02iuK6FUUVM" crossorigin="anonymous">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css"
          integrity="sha512-..." crossorigin="anonymous" />
    <link rel="stylesheet" href="css/Global.css">
</head>

<div class="main-content-wrapper">
    <div class="container d-flex flex-column align-items-center justify-content-center vw-100">
        <div class="d-flex flex-column justify-content-center align-items-center w-100 mt-md-3">
            <div class="mb-3">
                <h2>新規登録</h2>
            </div>
            <form action="sighin.html" method="post" class="border rounded shadow p-4 w-100 login-form">
                <input type="hidden" name="func" value="" />
                <input type="hidden" name="param" value="" />
                <div class="form-group my-2 px-2 w-100">
                    <label>ユーザーネーム <span class="text-danger fw-bold">*</span></label>
                    <input type="text" name="user_name" class="form-control" placeholder="ユーザーネーム" required>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>メールアドレス <span class="text-danger fw-bold">*</span></label>
                    <input type="email" name="user_mailaddress" class="form-control" placeholder="メールアドレス" required>
                </div>
                <div class="form-group my-2 px-2 w-100">
                    <label>パスワード <span class="text-danger fw-bold">*</span></label>
                    <input type="password" name="user_password" class="form-control" placeholder="パスワード" required>
                </div>
                <div class="text-center">
                    <p>利用規約は <a href="">こちらから</a></p>
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <input type="submit" class="btn btn-primary w-100" value="新規登録">
                </div>
                <div class="form-group d-flex justify-content-center my-2 px-2 w-100">
                    <a href="index.php">ログインはこちら</a>
                </div>
            </form>
        </div>
    </div>
</div>
